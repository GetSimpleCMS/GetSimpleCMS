<?php

declare (strict_types=1);
namespace Rector\PhpDocParser\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use Rector\PhpDocParser\PhpDocParser\Contract\PhpDocNodeVisitorInterface;
use Rector\PhpDocParser\PhpDocParser\Exception\InvalidTraverseException;
use Rector\PhpDocParser\PhpDocParser\PhpDocNodeVisitor\CallablePhpDocNodeVisitor;
/**
 * @api
 *
 * Mimics
 * https://github.com/nikic/PHP-Parser/blob/4abdcde5f16269959a834e4e58ea0ba0938ab133/lib/PhpParser/NodeTraverser.php
 *
 * @see \Rector\Tests\PhpDocParser\PhpDocParser\SimplePhpDocNodeTraverser\PhpDocNodeTraverserTest
 */
final class PhpDocNodeTraverser
{
    /**
     * If NodeVisitor::enterNode() returns DONT_TRAVERSE_CHILDREN, child nodes of the current node will not be traversed
     * for any visitors.
     *
     * For subsequent visitors enterNode() will still be called on the current node and leaveNode() will also be invoked
     * for the current node.
     *
     * @api
     * @var int
     */
    public const DONT_TRAVERSE_CHILDREN = 1;
    /**
     * If NodeVisitor::enterNode() or NodeVisitor::leaveNode() returns STOP_TRAVERSAL, traversal is aborted.
     *
     * The afterTraverse() method will still be invoked.
     *
     * @api
     * @var int
     */
    public const STOP_TRAVERSAL = 2;
    /**
     * If NodeVisitor::leaveNode() returns NODE_REMOVE for a node that occurs in an array, it will be removed from the
     * array.
     *
     * For subsequent visitors leaveNode() will still be invoked for the removed node.
     *
     * @api
     * @var int
     */
    public const NODE_REMOVE = 3;
    /**
     * If NodeVisitor::enterNode() returns DONT_TRAVERSE_CURRENT_AND_CHILDREN, child nodes of the current node will not
     * be traversed for any visitors.
     *
     * For subsequent visitors enterNode() will not be called as well. leaveNode() will be invoked for visitors that has
     * enterNode() method invoked.
     *
     * @api
     * @var int
     */
    public const DONT_TRAVERSE_CURRENT_AND_CHILDREN = 4;
    /**
     * @var bool Whether traversal should be stopped
     */
    private $stopTraversal = \false;
    /**
     * @var PhpDocNodeVisitorInterface[]
     */
    private $phpDocNodeVisitors = [];
    public function addPhpDocNodeVisitor(PhpDocNodeVisitorInterface $phpDocNodeVisitor) : void
    {
        $this->phpDocNodeVisitors[] = $phpDocNodeVisitor;
    }
    public function traverse(Node $node) : void
    {
        foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
            $phpDocNodeVisitor->beforeTraverse($node);
        }
        $node = $this->traverseNode($node);
        foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
            $phpDocNodeVisitor->afterTraverse($node);
        }
    }
    /**
     * @param callable(Node $node): (int|null|Node) $callable
     */
    public function traverseWithCallable(Node $node, string $docContent, callable $callable) : void
    {
        $callablePhpDocNodeVisitor = new CallablePhpDocNodeVisitor($callable, $docContent);
        $this->addPhpDocNodeVisitor($callablePhpDocNodeVisitor);
        $this->traverse($node);
    }
    /**
     * @template TNode of Node
     * @param TNode $node
     * @return TNode
     */
    private function traverseNode(Node $node) : Node
    {
        $objectPublicPropertiesToValues = \get_object_vars($node);
        $subNodeNames = \array_keys($objectPublicPropertiesToValues);
        foreach ($subNodeNames as $subNodeName) {
            $subNode =& $node->{$subNodeName};
            if (\is_array($subNode)) {
                $subNode = $this->traverseArray($subNode);
            } elseif ($subNode instanceof Node) {
                $breakVisitorIndex = null;
                $traverseChildren = \true;
                foreach ($this->phpDocNodeVisitors as $visitorIndex => $phpDocNodeVisitor) {
                    $return = $phpDocNodeVisitor->enterNode($subNode);
                    if ($return !== null) {
                        if ($return instanceof Node) {
                            $subNode = $return;
                        } elseif ($return === self::DONT_TRAVERSE_CHILDREN) {
                            $traverseChildren = \false;
                        } elseif ($return === self::DONT_TRAVERSE_CURRENT_AND_CHILDREN) {
                            $traverseChildren = \false;
                            $breakVisitorIndex = $visitorIndex;
                            break;
                        } elseif ($return === self::STOP_TRAVERSAL) {
                            $this->stopTraversal = \true;
                        } elseif ($return === self::NODE_REMOVE) {
                            $subNode = null;
                            continue 2;
                        } else {
                            throw new InvalidTraverseException('enterNode() returned invalid value of type ' . \gettype($return));
                        }
                    }
                }
                if ($traverseChildren) {
                    $subNode = $this->traverseNode($subNode);
                    if ($this->stopTraversal) {
                        break;
                    }
                }
                foreach ($this->phpDocNodeVisitors as $visitorIndex => $phpDocNodeVisitor) {
                    $phpDocNodeVisitor->leaveNode($subNode);
                    if ($breakVisitorIndex === $visitorIndex) {
                        break;
                    }
                }
            }
        }
        return $node;
    }
    /**
     * @param array<Node|mixed> $nodes
     * @return array<Node|mixed>
     */
    private function traverseArray(array $nodes) : array
    {
        foreach ($nodes as $key => &$node) {
            // can be string or something else
            if (!$node instanceof Node) {
                continue;
            }
            $traverseChildren = \true;
            $breakVisitorIndex = null;
            foreach ($this->phpDocNodeVisitors as $visitorIndex => $phpDocNodeVisitor) {
                $return = $phpDocNodeVisitor->enterNode($node);
                if ($return !== null) {
                    if ($return instanceof Node) {
                        $node = $return;
                    } elseif ($return === self::DONT_TRAVERSE_CHILDREN) {
                        $traverseChildren = \false;
                    } elseif ($return === self::DONT_TRAVERSE_CURRENT_AND_CHILDREN) {
                        $traverseChildren = \false;
                        $breakVisitorIndex = $visitorIndex;
                        break;
                    } elseif ($return === self::STOP_TRAVERSAL) {
                        $this->stopTraversal = \true;
                    } elseif ($return === self::NODE_REMOVE) {
                        // remove node
                        unset($nodes[$key]);
                        continue 2;
                    } else {
                        throw new InvalidTraverseException('enterNode() returned invalid value of type ' . \gettype($return));
                    }
                }
            }
            // should traverse node childrens properties?
            if ($traverseChildren) {
                $node = $this->traverseNode($node);
                if ($this->stopTraversal) {
                    break;
                }
            }
            foreach ($this->phpDocNodeVisitors as $visitorIndex => $phpDocNodeVisitor) {
                $return = $phpDocNodeVisitor->leaveNode($node);
                if ($return !== null) {
                    if ($return instanceof Node) {
                        $node = $return;
                    } elseif (\is_array($return)) {
                        $doNodes[] = [$key, $return];
                        break;
                    } elseif ($return === self::NODE_REMOVE) {
                        $doNodes[] = [$key, []];
                        break;
                    } elseif ($return === self::STOP_TRAVERSAL) {
                        $this->stopTraversal = \true;
                        break 2;
                    } else {
                        throw new InvalidTraverseException('leaveNode() returned invalid value of type ' . \gettype($return));
                    }
                }
                if ($breakVisitorIndex === $visitorIndex) {
                    break;
                }
            }
        }
        return $nodes;
    }
}
