<?php

declare (strict_types=1);
namespace Rector\Symfony\ValueObject;

use Rector\Symfony\Contract\Tag\TagInterface;
final class ServiceDefinition
{
    /**
     * @readonly
     * @var string
     */
    private $id;
    /**
     * @readonly
     * @var string|null
     */
    private $class;
    /**
     * @readonly
     * @var bool
     */
    private $isPublic;
    /**
     * @readonly
     * @var bool
     */
    private $isSynthetic;
    /**
     * @readonly
     * @var string|null
     */
    private $alias;
    /**
     * @var TagInterface[]
     * @readonly
     */
    private $tags;
    /**
     * @param TagInterface[] $tags
     */
    public function __construct(string $id, ?string $class, bool $isPublic, bool $isSynthetic, ?string $alias, array $tags)
    {
        $this->id = $id;
        $this->class = $class;
        $this->isPublic = $isPublic;
        $this->isSynthetic = $isSynthetic;
        $this->alias = $alias;
        $this->tags = $tags;
    }
    public function getId() : string
    {
        return $this->id;
    }
    public function getClass() : ?string
    {
        return $this->class;
    }
    public function isPublic() : bool
    {
        return $this->isPublic;
    }
    public function isSynthetic() : bool
    {
        return $this->isSynthetic;
    }
    public function getAlias() : ?string
    {
        return $this->alias;
    }
    /**
     * @return TagInterface[]
     */
    public function getTags() : array
    {
        return $this->tags;
    }
    public function getTag(string $name) : ?TagInterface
    {
        foreach ($this->tags as $tag) {
            if ($tag->getName() !== $name) {
                continue;
            }
            return $tag;
        }
        return null;
    }
}
