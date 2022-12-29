<?php

declare (strict_types=1);
namespace Rector\BetterPhpDocParser\ValueObject\PhpDoc\DoctrineAnnotation;

use PHPStan\PhpDocParser\Ast\NodeAttributes;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use Rector\BetterPhpDocParser\PhpDoc\ArrayItemNode;
use Rector\BetterPhpDocParser\ValueObject\PhpDocAttributeKey;
abstract class AbstractValuesAwareNode implements PhpDocTagValueNode
{
    use NodeAttributes;
    /**
     * @var bool
     */
    protected $hasChanged = \false;
    /**
     * @var ArrayItemNode[]
     */
    public $values = [];
    /**
     * @var string|null
     */
    protected $originalContent;
    /**
     * @var string|null
     */
    protected $silentKey;
    /**
     * @param ArrayItemNode[] $values Must be public so node traverser can go through them
     */
    public function __construct(array $values = [], ?string $originalContent = null, ?string $silentKey = null)
    {
        $this->values = $values;
        $this->originalContent = $originalContent;
        $this->silentKey = $silentKey;
    }
    /**
     * @api
     */
    public function removeValue(string $desiredKey) : void
    {
        foreach ($this->values as $key => $value) {
            if ($value->key !== $desiredKey) {
                continue;
            }
            unset($this->values[$key]);
            // invoke reprint
            $this->setAttribute(PhpDocAttributeKey::ORIG_NODE, null);
        }
    }
    /**
     * @return ArrayItemNode[]
     */
    public function getValues() : array
    {
        return $this->values;
    }
    /**
     * @return ArrayItemNode[]
     */
    public function getValuesWithSilentKey() : array
    {
        if ($this->silentKey === null) {
            return $this->values;
        }
        // to keep original values untouched, unless not changed
        $silentKeyAwareValues = $this->values;
        foreach ($silentKeyAwareValues as $silentKeyAwareValue) {
            if ($silentKeyAwareValue->key === null) {
                $silentKeyAwareValue->key = $this->silentKey;
                break;
            }
        }
        return $silentKeyAwareValues;
    }
    public function getValue(string $desiredKey) : ?ArrayItemNode
    {
        foreach ($this->values as $value) {
            if ($value->key === $desiredKey) {
                return $value;
            }
        }
        return null;
    }
    public function getSilentValue() : ?ArrayItemNode
    {
        foreach ($this->values as $value) {
            if ($value->key === null) {
                return $value;
            }
        }
        return null;
    }
    public function markAsChanged() : void
    {
        $this->hasChanged = \true;
    }
    /**
     * @param mixed[] $values
     */
    protected function printValuesContent(array $values) : string
    {
        $itemContents = '';
        \end($values);
        $lastItemKey = \key($values);
        foreach ($values as $key => $value) {
            if (\is_int($key)) {
                $itemContents .= $this->stringifyValue($value);
            } else {
                $itemContents .= $key . '=' . $this->stringifyValue($value);
            }
            if ($lastItemKey !== $key) {
                $itemContents .= ', ';
            }
        }
        return $itemContents;
    }
    /**
     * @param mixed $value
     */
    private function stringifyValue($value) : string
    {
        // @todo resolve original casing
        if ($value === \false) {
            return 'false';
        }
        if ($value === \true) {
            return 'true';
        }
        if (\is_int($value)) {
            return (string) $value;
        }
        if (\is_float($value)) {
            return (string) $value;
        }
        if (\is_array($value)) {
            return $this->printValuesContent($value);
        }
        return (string) $value;
    }
}
