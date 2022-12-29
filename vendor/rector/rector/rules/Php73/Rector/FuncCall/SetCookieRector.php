<?php

declare (strict_types=1);
namespace Rector\Php73\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\VariadicPlaceholder;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * Convert legacy setcookie arguments to new array options
 *
 * @see \Rector\Tests\Php73\Rector\FuncCall\SetcookieRector\SetCookieRectorTest
 *
 * @changelog https://www.php.net/setcookie https://wiki.php.net/rfc/same-site-cookie
 */
final class SetCookieRector extends AbstractRector implements MinPhpVersionInterface
{
    /**
     * Conversion table from argument index to options name
     * @var array<int, string>
     */
    private const KNOWN_OPTIONS = [2 => 'expires', 3 => 'path', 4 => 'domain', 5 => 'secure', 6 => 'httponly'];
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Convert setcookie argument to PHP7.3 option array', [new CodeSample(<<<'CODE_SAMPLE'
setcookie('name', $value, 360);
CODE_SAMPLE
, <<<'CODE_SAMPLE'
setcookie('name', $value, ['expires' => 360]);
CODE_SAMPLE
), new CodeSample(<<<'CODE_SAMPLE'
setcookie('name', $name, 0, '', '', true, true);
CODE_SAMPLE
, <<<'CODE_SAMPLE'
setcookie('name', $name, ['expires' => 0, 'path' => '', 'domain' => '', 'secure' => true, 'httponly' => true]);
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
        if ($this->shouldSkip($node)) {
            return null;
        }
        $node->args = $this->composeNewArgs($node);
        return $node;
    }
    public function provideMinPhpVersion() : int
    {
        return PhpVersionFeature::SETCOOKIE_ACCEPT_ARRAY_OPTIONS;
    }
    private function shouldSkip(FuncCall $funcCall) : bool
    {
        if (!$this->isNames($funcCall, ['setcookie', 'setrawcookie'])) {
            return \true;
        }
        $argsCount = \count($funcCall->args);
        if ($argsCount <= 2) {
            return \true;
        }
        if ($funcCall->args[2] instanceof Arg && $funcCall->args[2]->value instanceof Array_) {
            return \true;
        }
        if ($argsCount === 3) {
            return $funcCall->args[2] instanceof Arg && $funcCall->args[2]->value instanceof Variable;
        }
        return \false;
    }
    /**
     * @return Arg[]|VariadicPlaceholder[]
     */
    private function composeNewArgs(FuncCall $funcCall) : array
    {
        $items = [];
        $args = $funcCall->args;
        $newArgs = [];
        $newArgs[] = $args[0];
        $newArgs[] = $args[1];
        unset($args[0]);
        unset($args[1]);
        foreach ($args as $idx => $arg) {
            $newKey = new String_(self::KNOWN_OPTIONS[$idx]);
            $items[] = new ArrayItem($arg->value, $newKey);
        }
        $newArgs[] = new Arg(new Array_($items));
        return $newArgs;
    }
}
