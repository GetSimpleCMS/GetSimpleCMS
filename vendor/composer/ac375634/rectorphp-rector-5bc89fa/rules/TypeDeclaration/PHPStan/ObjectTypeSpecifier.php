<?php

declare (strict_types=1);
namespace Rector\TypeDeclaration\PHPStan;

use RectorPrefix202212\Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\UnionType;
use Rector\Naming\Naming\UseImportsResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\StaticTypeMapper\Naming\NameScopeFactory;
use Rector\StaticTypeMapper\ValueObject\Type\AliasedObjectType;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Rector\StaticTypeMapper\ValueObject\Type\NonExistingObjectType;
use Rector\StaticTypeMapper\ValueObject\Type\ShortenedGenericObjectType;
use Rector\StaticTypeMapper\ValueObject\Type\ShortenedObjectType;
use Rector\TypeDeclaration\Contract\PHPStan\TypeWithClassTypeSpecifierInterface;
final class ObjectTypeSpecifier
{
    /**
     * @readonly
     * @var \PHPStan\Reflection\ReflectionProvider
     */
    private $reflectionProvider;
    /**
     * @readonly
     * @var \Rector\Naming\Naming\UseImportsResolver
     */
    private $useImportsResolver;
    /**
     * @var TypeWithClassTypeSpecifierInterface[]
     * @readonly
     */
    private $typeWithClassTypeSpecifiers;
    /**
     * @param TypeWithClassTypeSpecifierInterface[] $typeWithClassTypeSpecifiers
     */
    public function __construct(ReflectionProvider $reflectionProvider, UseImportsResolver $useImportsResolver, array $typeWithClassTypeSpecifiers)
    {
        $this->reflectionProvider = $reflectionProvider;
        $this->useImportsResolver = $useImportsResolver;
        $this->typeWithClassTypeSpecifiers = $typeWithClassTypeSpecifiers;
    }
    /**
     * @return \PHPStan\Type\TypeWithClassName|\Rector\StaticTypeMapper\ValueObject\Type\NonExistingObjectType|\PHPStan\Type\UnionType|\PHPStan\Type\MixedType
     */
    public function narrowToFullyQualifiedOrAliasedObjectType(Node $node, ObjectType $objectType, ?\PHPStan\Analyser\Scope $scope)
    {
        //        $nameScope = $this->nameScopeFactory->createNameScopeFromNodeWithoutTemplateTypes($node);
        // @todo reuse name scope
        if ($scope instanceof Scope) {
            foreach ($this->typeWithClassTypeSpecifiers as $typeWithClassTypeSpecifier) {
                if ($typeWithClassTypeSpecifier->match($objectType, $scope)) {
                    return $typeWithClassTypeSpecifier->resolveObjectReferenceType($objectType, $scope);
                }
            }
        }
        $uses = $this->useImportsResolver->resolveForNode($node);
        if ($uses === []) {
            if (!$this->reflectionProvider->hasClass($objectType->getClassName())) {
                return new NonExistingObjectType($objectType->getClassName());
            }
            return new FullyQualifiedObjectType($objectType->getClassName(), null, $objectType->getClassReflection());
        }
        $aliasedObjectType = $this->matchAliasedObjectType($node, $objectType, $uses);
        if ($aliasedObjectType !== null) {
            return $aliasedObjectType;
        }
        $shortenedObjectType = $this->matchShortenedObjectType($objectType, $uses);
        if ($shortenedObjectType !== null) {
            return $shortenedObjectType;
        }
        $className = \ltrim($objectType->getClassName(), '\\');
        if ($this->reflectionProvider->hasClass($className)) {
            return new FullyQualifiedObjectType($className);
        }
        // invalid type
        return new NonExistingObjectType($className);
    }
    /**
     * @param Use_[]|GroupUse[] $uses
     */
    private function matchAliasedObjectType(Node $node, ObjectType $objectType, array $uses) : ?AliasedObjectType
    {
        if ($uses === []) {
            return null;
        }
        $className = $objectType->getClassName();
        $parentNode = $node->getAttribute(AttributeKey::PARENT_NODE);
        foreach ($uses as $use) {
            $prefix = $this->useImportsResolver->resolvePrefix($use);
            foreach ($use->uses as $useUse) {
                if ($useUse->alias === null) {
                    continue;
                }
                $useName = $prefix . $useUse->name->toString();
                $alias = $useUse->alias->toString();
                $fullyQualifiedName = $prefix . $useUse->name->toString();
                $processAliasedObject = $this->processAliasedObject($alias, $className, $useName, $parentNode, $fullyQualifiedName);
                if ($processAliasedObject instanceof AliasedObjectType) {
                    return $processAliasedObject;
                }
            }
        }
        return null;
    }
    private function processAliasedObject(string $alias, string $className, string $useName, ?Node $parentNode, string $fullyQualifiedName) : ?AliasedObjectType
    {
        // A. is alias in use statement matching this class alias
        if ($alias === $className) {
            return new AliasedObjectType($alias, $fullyQualifiedName);
        }
        // B. is aliased classes matching the class name and parent node is MethodCall/StaticCall
        if ($useName === $className && ($parentNode instanceof MethodCall || $parentNode instanceof StaticCall)) {
            return new AliasedObjectType($useName, $fullyQualifiedName);
        }
        // C. is aliased classes matching the class name
        if ($useName === $className) {
            return new AliasedObjectType($alias, $fullyQualifiedName);
        }
        return null;
    }
    /**
     * @param Use_[]|GroupUse[] $uses
     * @return \Rector\StaticTypeMapper\ValueObject\Type\ShortenedObjectType|\Rector\StaticTypeMapper\ValueObject\Type\ShortenedGenericObjectType|null
     */
    private function matchShortenedObjectType(ObjectType $objectType, array $uses)
    {
        if ($uses === []) {
            return null;
        }
        foreach ($uses as $use) {
            $prefix = $use instanceof GroupUse ? $use->prefix . '\\' : '';
            foreach ($use->uses as $useUse) {
                if ($useUse->alias !== null) {
                    continue;
                }
                $partialNamespaceObjectType = $this->matchPartialNamespaceObjectType($prefix, $objectType, $useUse);
                if ($partialNamespaceObjectType !== null) {
                    return $partialNamespaceObjectType;
                }
                $partialNamespaceObjectType = $this->matchClassWithLastUseImportPart($prefix, $objectType, $useUse);
                if ($partialNamespaceObjectType instanceof FullyQualifiedObjectType) {
                    // keep Generic items
                    if ($objectType instanceof GenericObjectType) {
                        return new ShortenedGenericObjectType($objectType->getClassName(), $objectType->getTypes(), $partialNamespaceObjectType->getClassName());
                    }
                    return $partialNamespaceObjectType->getShortNameType();
                }
                if ($partialNamespaceObjectType instanceof ShortenedObjectType) {
                    return $partialNamespaceObjectType;
                }
            }
        }
        return null;
    }
    private function matchPartialNamespaceObjectType(string $prefix, ObjectType $objectType, UseUse $useUse) : ?ShortenedObjectType
    {
        // partial namespace
        if (\strncmp($objectType->getClassName(), $useUse->name->getLast() . '\\', \strlen($useUse->name->getLast() . '\\')) !== 0) {
            return null;
        }
        $classNameWithoutLastUsePart = Strings::after($objectType->getClassName(), '\\', 1);
        $connectedClassName = $prefix . $useUse->name->toString() . '\\' . $classNameWithoutLastUsePart;
        if (!$this->reflectionProvider->hasClass($connectedClassName)) {
            return null;
        }
        if ($objectType->getClassName() === $connectedClassName) {
            return null;
        }
        return new ShortenedObjectType($objectType->getClassName(), $connectedClassName);
    }
    /**
     * @return FullyQualifiedObjectType|ShortenedObjectType|null
     */
    private function matchClassWithLastUseImportPart(string $prefix, ObjectType $objectType, UseUse $useUse) : ?ObjectType
    {
        if ($useUse->name->getLast() !== $objectType->getClassName()) {
            return null;
        }
        if (!$this->reflectionProvider->hasClass($prefix . $useUse->name->toString())) {
            return null;
        }
        if ($objectType->getClassName() === $prefix . $useUse->name->toString()) {
            return new FullyQualifiedObjectType($objectType->getClassName());
        }
        return new ShortenedObjectType($objectType->getClassName(), $prefix . $useUse->name->toString());
    }
}
