<?php

declare (strict_types=1);
namespace Rector\Transform\Rector\Assign;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractRector;
use Rector\Transform\ValueObject\PropertyFetchToMethodCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use RectorPrefix202212\Webmozart\Assert\Assert;
/**
 * @see \Rector\Tests\Transform\Rector\Assign\PropertyFetchToMethodCallRector\PropertyFetchToMethodCallRectorTest
 */
final class PropertyFetchToMethodCallRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var PropertyFetchToMethodCall[]
     */
    private $propertiesToMethodCalls = [];
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Replaces properties assign calls be defined methods.', [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
$result = $object->property;
$object->property = $value;

$bare = $object->bareProperty;
CODE_SAMPLE
, <<<'CODE_SAMPLE'
$result = $object->getProperty();
$object->setProperty($value);

$bare = $object->getConfig('someArg');
CODE_SAMPLE
, [new PropertyFetchToMethodCall('SomeObject', 'property', 'getProperty', 'setProperty'), new PropertyFetchToMethodCall('SomeObject', 'bareProperty', 'getConfig', null, ['someArg'])])]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [Assign::class, PropertyFetch::class];
    }
    /**
     * @param PropertyFetch|Assign $node
     */
    public function refactor(Node $node) : ?Node
    {
        if ($node instanceof Assign && $node->var instanceof PropertyFetch) {
            return $this->processSetter($node);
        }
        if ($node instanceof PropertyFetch) {
            return $this->processGetter($node);
        }
        return null;
    }
    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration) : void
    {
        Assert::allIsAOf($configuration, PropertyFetchToMethodCall::class);
        $this->propertiesToMethodCalls = $configuration;
    }
    private function processSetter(Assign $assign) : ?Node
    {
        /** @var PropertyFetch $propertyFetchNode */
        $propertyFetchNode = $assign->var;
        $propertyToMethodCall = $this->matchPropertyFetchCandidate($propertyFetchNode);
        if (!$propertyToMethodCall instanceof PropertyFetchToMethodCall) {
            return null;
        }
        if ($propertyToMethodCall->getNewSetMethod() === null) {
            throw new ShouldNotHappenException();
        }
        $args = $this->nodeFactory->createArgs([$assign->expr]);
        /** @var Variable $variable */
        $variable = $propertyFetchNode->var;
        return $this->nodeFactory->createMethodCall($variable, $propertyToMethodCall->getNewSetMethod(), $args);
    }
    private function processGetter(PropertyFetch $propertyFetch) : ?Node
    {
        $propertyToMethodCall = $this->matchPropertyFetchCandidate($propertyFetch);
        if (!$propertyToMethodCall instanceof PropertyFetchToMethodCall) {
            return null;
        }
        // simple method name
        if ($propertyToMethodCall->getNewGetMethod() !== '') {
            $methodCall = $this->nodeFactory->createMethodCall($propertyFetch->var, $propertyToMethodCall->getNewGetMethod());
            if ($propertyToMethodCall->getNewGetArguments() !== []) {
                $methodCall->args = $this->nodeFactory->createArgs($propertyToMethodCall->getNewGetArguments());
            }
            return $methodCall;
        }
        return $propertyFetch;
    }
    private function matchPropertyFetchCandidate(PropertyFetch $propertyFetch) : ?PropertyFetchToMethodCall
    {
        foreach ($this->propertiesToMethodCalls as $propertyToMethodCall) {
            if (!$this->isObjectType($propertyFetch->var, $propertyToMethodCall->getOldObjectType())) {
                continue;
            }
            if (!$this->isName($propertyFetch, $propertyToMethodCall->getOldProperty())) {
                continue;
            }
            return $propertyToMethodCall;
        }
        return null;
    }
}
