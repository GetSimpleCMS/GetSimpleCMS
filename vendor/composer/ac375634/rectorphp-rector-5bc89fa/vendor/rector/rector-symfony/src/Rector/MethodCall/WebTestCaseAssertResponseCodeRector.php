<?php

declare (strict_types=1);
namespace Rector\Symfony\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Rector\Symfony\NodeAnalyzer\SymfonyTestCaseAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @changelog https://symfony.com/blog/new-in-symfony-4-3-better-test-assertions
 * @changelog https://github.com/symfony/symfony/pull/30813
 *
 * @see \Rector\Symfony\Tests\Rector\MethodCall\WebTestCaseAssertResponseCodeRector\WebTestCaseAssertResponseCodeRectorTest
 */
final class WebTestCaseAssertResponseCodeRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\Symfony\NodeAnalyzer\SymfonyTestCaseAnalyzer
     */
    private $symfonyTestCaseAnalyzer;
    /**
     * @readonly
     * @var \Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer
     */
    private $testsNodeAnalyzer;
    public function __construct(SymfonyTestCaseAnalyzer $symfonyTestCaseAnalyzer, TestsNodeAnalyzer $testsNodeAnalyzer)
    {
        $this->symfonyTestCaseAnalyzer = $symfonyTestCaseAnalyzer;
        $this->testsNodeAnalyzer = $testsNodeAnalyzer;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Simplify use of assertions in WebTestCase', [new CodeSample(<<<'CODE_SAMPLE'
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SomeClass extends WebTestCase
{
    public function test()
    {
        $response = self::getClient()->getResponse();

        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('https://example.com', $response->headers->get('Location'));
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SomeClass extends WebTestCase
{
    public function test()
    {
        $this->assertResponseStatusCodeSame(301);
        $this->assertResponseRedirects('https://example.com');
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
        return [MethodCall::class, StaticCall::class];
    }
    /**
     * @param MethodCall|StaticCall $node
     */
    public function refactor(Node $node) : ?Node
    {
        if (!$this->symfonyTestCaseAnalyzer->isInWebTestCase($node)) {
            return null;
        }
        $newMethodCall = $this->processAssertResponseStatusCodeSame($node);
        if ($newMethodCall !== null) {
            return $newMethodCall;
        }
        return $this->processAssertResponseRedirects($node);
    }
    /**
     * We look for: "$client->getResponse()->headers->get('Location')"
     */
    public function isGetLocationMethodCall(Expr $expr) : bool
    {
        if (!$expr instanceof MethodCall) {
            return \false;
        }
        if (!$this->isName($expr->name, 'get')) {
            return \false;
        }
        $args = $expr->getArgs();
        if ($args === []) {
            return \false;
        }
        $firstArg = $args[0];
        return $this->valueResolver->isValue($firstArg->value, 'Location');
    }
    /**
     * @param \PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\MethodCall $methodCall
     * @return \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall|null
     */
    private function processAssertResponseStatusCodeSame($methodCall)
    {
        if (!$this->isName($methodCall->name, 'assertSame')) {
            return null;
        }
        $args = $methodCall->getArgs();
        $secondArg = $args[1];
        if (!$secondArg->value instanceof MethodCall) {
            return null;
        }
        $nestedMethodCall = $secondArg->value;
        // caller must be a response object
        if (!$this->isObjectType($nestedMethodCall->var, new ObjectType('Symfony\\Component\\HttpFoundation\\Response'))) {
            return null;
        }
        if (!$this->nodeNameResolver->isName($nestedMethodCall->name, 'getStatusCode')) {
            return null;
        }
        $statusCode = $this->valueResolver->getValue($args[0]->value, \true);
        // handled by another method
        if ($statusCode === 200) {
            return null;
        }
        $newArgs = [$methodCall->args[0]];
        // When we had a custom message argument we want to add it to the new assert.
        if (isset($args[2])) {
            if ($args[2]->value instanceof FuncCall) {
                $newArgs[] = $args[2]->value;
            } else {
                $newArgs[] = $this->valueResolver->getValue($args[2]->value, \true);
            }
        }
        if ($methodCall instanceof StaticCall) {
            return $this->nodeFactory->createStaticCall('self', 'assertResponseStatusCodeSame', $newArgs);
        }
        return $this->nodeFactory->createLocalMethodCall('assertResponseStatusCodeSame', $newArgs);
    }
    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $methodCall
     * @return \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall|null
     */
    private function processAssertResponseRedirects($methodCall)
    {
        if (!$this->testsNodeAnalyzer->isPHPUnitMethodCallNames($methodCall, ['assertSame'])) {
            return null;
        }
        $args = $methodCall->getArgs();
        $firstArgValue = $args[1]->value;
        if (!$this->isGetLocationMethodCall($firstArgValue)) {
            return null;
        }
        $newArgs = [$methodCall->args[0]];
        if (isset($args[2])) {
            // When we had a $message argument we want to add it to the new assert together with $expectedCode null.
            $newArgs[] = null;
            if ($args[2]->value instanceof FuncCall) {
                $newArgs[] = $args[2]->value;
            } else {
                $newArgs[] = $this->valueResolver->getValue($args[2]->value, \true);
            }
        }
        if ($methodCall instanceof StaticCall) {
            return $this->nodeFactory->createStaticCall('self', 'assertResponseRedirects', $newArgs);
        }
        return $this->nodeFactory->createLocalMethodCall('assertResponseRedirects', $newArgs);
    }
}
