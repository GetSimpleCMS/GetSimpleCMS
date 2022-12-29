<?php

declare (strict_types=1);
namespace Rector\Symfony\TypeDeclaration;

use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\Type\ArrayType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\UnionType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\ValueObject\Type\FullyQualifiedIdentifierTypeNode;
use Rector\Core\Php\PhpVersionProvider;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\StaticTypeMapper\StaticTypeMapper;
final class ReturnTypeDeclarationUpdater
{
    /**
     * @readonly
     * @var \Rector\Core\Php\PhpVersionProvider
     */
    private $phpVersionProvider;
    /**
     * @readonly
     * @var \Rector\StaticTypeMapper\StaticTypeMapper
     */
    private $staticTypeMapper;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory
     */
    private $phpDocInfoFactory;
    public function __construct(PhpVersionProvider $phpVersionProvider, StaticTypeMapper $staticTypeMapper, PhpDocInfoFactory $phpDocInfoFactory)
    {
        $this->phpVersionProvider = $phpVersionProvider;
        $this->staticTypeMapper = $staticTypeMapper;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
    }
    /**
     * @param class-string $className
     */
    public function updateClassMethod(ClassMethod $classMethod, string $className) : void
    {
        $this->updatePhpDoc($classMethod, $className);
        $this->updatePhp($classMethod, $className);
    }
    /**
     * @param class-string $className
     */
    private function updatePhpDoc(ClassMethod $classMethod, string $className) : void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethod);
        $returnTagValueNode = $phpDocInfo->getReturnTagValue();
        if (!$returnTagValueNode instanceof ReturnTagValueNode) {
            return;
        }
        $returnStaticType = $this->staticTypeMapper->mapPHPStanPhpDocTypeNodeToPHPStanType($returnTagValueNode->type, $classMethod);
        if ($returnStaticType instanceof ArrayType || $returnStaticType instanceof UnionType) {
            $returnTagValueNode->type = new FullyQualifiedIdentifierTypeNode($className);
        }
    }
    /**
     * @param class-string $className
     */
    private function updatePhp(ClassMethod $classMethod, string $className) : void
    {
        if (!$this->phpVersionProvider->isAtLeastPhpVersion(PhpVersionFeature::SCALAR_TYPES)) {
            return;
        }
        $objectType = new ObjectType($className);
        // change return type
        if ($classMethod->returnType !== null) {
            $returnType = $this->staticTypeMapper->mapPhpParserNodePHPStanType($classMethod->returnType);
            if ($objectType->isSuperTypeOf($returnType)->yes()) {
                return;
            }
        }
        $classMethod->returnType = new FullyQualified($className);
    }
}
