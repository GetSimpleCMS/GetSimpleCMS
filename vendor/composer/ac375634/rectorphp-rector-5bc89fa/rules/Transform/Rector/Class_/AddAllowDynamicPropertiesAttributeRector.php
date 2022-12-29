<?php

declare (strict_types=1);
namespace Rector\Transform\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Core\Contract\Rector\AllowEmptyConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\ValueObject\MethodName;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\FamilyTree\Reflection\FamilyRelationsAnalyzer;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\Php81\Enum\AttributeName;
use Rector\PhpAttribute\NodeFactory\PhpAttributeGroupFactory;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use RectorPrefix202212\Webmozart\Assert\Assert;
/**
 * @changelog https://wiki.php.net/rfc/deprecate_dynamic_properties
 *
 * @see \Rector\Tests\Transform\Rector\Class_\AddAllowDynamicPropertiesAttributeRector\AddAllowDynamicPropertiesAttributeRectorTest
 */
final class AddAllowDynamicPropertiesAttributeRector extends AbstractRector implements AllowEmptyConfigurableRectorInterface, MinPhpVersionInterface
{
    /**
     * @var array<array-key, string>
     */
    private $transformOnNamespaces = [];
    /**
     * @readonly
     * @var \Rector\FamilyTree\Reflection\FamilyRelationsAnalyzer
     */
    private $familyRelationsAnalyzer;
    /**
     * @readonly
     * @var \Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer
     */
    private $phpAttributeAnalyzer;
    /**
     * @readonly
     * @var \Rector\PhpAttribute\NodeFactory\PhpAttributeGroupFactory
     */
    private $phpAttributeGroupFactory;
    /**
     * @readonly
     * @var \PHPStan\Reflection\ReflectionProvider
     */
    private $reflectionProvider;
    public function __construct(FamilyRelationsAnalyzer $familyRelationsAnalyzer, PhpAttributeAnalyzer $phpAttributeAnalyzer, PhpAttributeGroupFactory $phpAttributeGroupFactory, ReflectionProvider $reflectionProvider)
    {
        $this->familyRelationsAnalyzer = $familyRelationsAnalyzer;
        $this->phpAttributeAnalyzer = $phpAttributeAnalyzer;
        $this->phpAttributeGroupFactory = $phpAttributeGroupFactory;
        $this->reflectionProvider = $reflectionProvider;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Add the `AllowDynamicProperties` attribute to all classes', [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
namespace Example\Domain;

class SomeObject {
    public string $someProperty = 'hello world';
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
namespace Example\Domain;

#[AllowDynamicProperties]
class SomeObject {
    public string $someProperty = 'hello world';
}
CODE_SAMPLE
, ['Example\\*'])]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [Class_::class];
    }
    public function configure(array $configuration) : void
    {
        $transformOnNamespaces = $configuration;
        Assert::allString($transformOnNamespaces);
        $this->transformOnNamespaces = $transformOnNamespaces;
    }
    /**
     * @param Class_ $node
     */
    public function refactor(Node $node) : ?Node
    {
        if ($this->shouldSkip($node)) {
            return null;
        }
        return $this->addAllowDynamicPropertiesAttribute($node);
    }
    public function provideMinPhpVersion() : int
    {
        return PhpVersionFeature::DEPRECATE_DYNAMIC_PROPERTIES;
    }
    private function isDescendantOfStdclass(Class_ $class) : bool
    {
        if (!$class->extends instanceof FullyQualified) {
            return \false;
        }
        $ancestorClassNames = $this->familyRelationsAnalyzer->getClassLikeAncestorNames($class);
        return \in_array('stdClass', $ancestorClassNames, \true);
    }
    private function hasNeededAttributeAlready(Class_ $class) : bool
    {
        $nodeHasAttribute = $this->phpAttributeAnalyzer->hasPhpAttribute($class, AttributeName::ALLOW_DYNAMIC_PROPERTIES);
        if ($nodeHasAttribute) {
            return \true;
        }
        if (!$class->extends instanceof FullyQualified) {
            return \false;
        }
        return $this->phpAttributeAnalyzer->hasInheritedPhpAttribute($class, AttributeName::ALLOW_DYNAMIC_PROPERTIES);
    }
    private function addAllowDynamicPropertiesAttribute(Class_ $class) : Class_
    {
        $class->attrGroups[] = $this->phpAttributeGroupFactory->createFromClass(AttributeName::ALLOW_DYNAMIC_PROPERTIES);
        return $class;
    }
    private function shouldSkip(Class_ $class) : bool
    {
        if ($this->transformOnNamespaces !== []) {
            $className = (string) $this->nodeNameResolver->getName($class);
            foreach ($this->transformOnNamespaces as $transformOnNamespace) {
                if (!$this->nodeNameResolver->isStringName($className, $transformOnNamespace)) {
                    return \true;
                }
            }
        }
        if ($this->isDescendantOfStdclass($class)) {
            return \true;
        }
        if ($this->hasNeededAttributeAlready($class)) {
            return \true;
        }
        return $this->hasMagicSetMethod($class);
    }
    private function hasMagicSetMethod(Class_ $class) : bool
    {
        $className = (string) $this->getName($class);
        $classReflection = $this->reflectionProvider->getClass($className);
        return $classReflection->hasMethod(MethodName::__SET);
    }
}
