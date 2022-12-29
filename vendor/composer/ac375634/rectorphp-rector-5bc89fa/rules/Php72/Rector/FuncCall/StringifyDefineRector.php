<?php

declare (strict_types=1);
namespace Rector\Php72\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\NodeTypeResolver\TypeAnalyzer\StringTypeAnalyzer;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @changelog https://3v4l.org/YiTeP
 * @see \Rector\Tests\Php72\Rector\FuncCall\StringifyDefineRector\StringifyDefineRectorTest
 */
final class StringifyDefineRector extends AbstractRector implements MinPhpVersionInterface
{
    /**
     * @readonly
     * @var \Rector\NodeTypeResolver\TypeAnalyzer\StringTypeAnalyzer
     */
    private $stringTypeAnalyzer;
    public function __construct(StringTypeAnalyzer $stringTypeAnalyzer)
    {
        $this->stringTypeAnalyzer = $stringTypeAnalyzer;
    }
    public function provideMinPhpVersion() : int
    {
        return PhpVersionFeature::STRING_IN_FIRST_DEFINE_ARG;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Make first argument of define() string', [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public function run(int $a)
    {
         define(CONSTANT_2, 'value');
         define('CONSTANT', 'value');
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(int $a)
    {
         define('CONSTANT_2', 'value');
         define('CONSTANT', 'value');
    }
}
CODE_SAMPLE
)]);
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
    public function refactor(Node $node) : ?Node
    {
        if (!$this->isName($node, 'define')) {
            return null;
        }
        if (!isset($node->args[0])) {
            return null;
        }
        if (!$node->args[0] instanceof Arg) {
            return null;
        }
        if ($this->stringTypeAnalyzer->isStringOrUnionStringOnlyType($node->args[0]->value)) {
            return null;
        }
        if ($node->args[0]->value instanceof ConstFetch) {
            $nodeName = $this->getName($node->args[0]->value);
            if ($nodeName === null) {
                return null;
            }
            $node->args[0]->value = new String_($nodeName);
        }
        return $node;
    }
}
