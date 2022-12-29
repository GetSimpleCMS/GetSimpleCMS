<?php

declare (strict_types=1);
namespace Rector\Arguments\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Rector\Arguments\ArgumentDefaultValueReplacer;
use Rector\Arguments\ValueObject\ReplaceFuncCallArgumentDefaultValue;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use RectorPrefix202212\Webmozart\Assert\Assert;
/**
 * @changelog https://php.watch/versions/8.1/version_compare-operator-restrictions
 * @changelog https://github.com/rectorphp/rector/issues/6271
 *
 * @see \Rector\Tests\Arguments\Rector\FuncCall\FunctionArgumentDefaultValueReplacerRector\FunctionArgumentDefaultValueReplacerRectorTest
 */
final class FunctionArgumentDefaultValueReplacerRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var ReplaceFuncCallArgumentDefaultValue[]
     */
    private $replacedArguments = [];
    /**
     * @readonly
     * @var \Rector\Arguments\ArgumentDefaultValueReplacer
     */
    private $argumentDefaultValueReplacer;
    public function __construct(ArgumentDefaultValueReplacer $argumentDefaultValueReplacer)
    {
        $this->argumentDefaultValueReplacer = $argumentDefaultValueReplacer;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Streamline the operator arguments of version_compare function', [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
version_compare(PHP_VERSION, '5.6', 'gte');
CODE_SAMPLE
, <<<'CODE_SAMPLE'
version_compare(PHP_VERSION, '5.6', 'ge');
CODE_SAMPLE
, [new ReplaceFuncCallArgumentDefaultValue('version_compare', 2, 'gte', 'ge')])]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [FuncCall::class];
    }
    /**
     * @param FuncCall $node
     */
    public function refactor(Node $node) : ?\PhpParser\Node\Expr\FuncCall
    {
        $hasChanged = \false;
        foreach ($this->replacedArguments as $replacedArgument) {
            if (!$this->isName($node->name, $replacedArgument->getFunction())) {
                continue;
            }
            $changedNode = $this->argumentDefaultValueReplacer->processReplaces($node, $replacedArgument);
            if ($changedNode instanceof Node) {
                $hasChanged = \true;
            }
        }
        if ($hasChanged) {
            return $node;
        }
        return null;
    }
    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration) : void
    {
        Assert::allIsAOf($configuration, ReplaceFuncCallArgumentDefaultValue::class);
        $this->replacedArguments = $configuration;
    }
}
