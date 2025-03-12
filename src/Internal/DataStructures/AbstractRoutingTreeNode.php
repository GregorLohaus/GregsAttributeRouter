<?php

declare(strict_types=1);

/**
 * @internal Internal namespace
 */
namespace Gregs\AttributeRouter\Internal\DataStructures;

use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use ReflectionClass;
use ReflectionMethod;

abstract class AbstractRoutingTreeNode
{
    protected function __construct(
        protected RoutingTreeNodeCollection $children = new RoutingTreeNodeCollection(),
        protected ?AbstractRoutingTreeNode $parent = null,
    ) {
    }

    abstract public function __invoke(): void;

    abstract public function allowedRoot(): bool;

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return AbstractRoutingTreeNode|RoutingTreeNodeCollection
     */
    abstract public static function fromReflection(ReflectionClass|ReflectionMethod $reflection): AbstractRoutingTreeNode|RoutingTreeNodeCollection;

    abstract public function getPathIdentifier(): string;

    final protected function setParent(AbstractRoutingTreeNode $parent): AbstractRoutingTreeNode
    {
        $this->parent = $parent;
        return $this;
    }

    final public function getParent(): ?AbstractRoutingTreeNode
    {
        return $this->parent;
    }

    final protected function addChild(AbstractRoutingTreeNode $child): AbstractRoutingTreeNode
    {
        $this->children->append($child->setParent($this));
        return $this;
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return AbstractRoutingTreeNode|RoutingTreeNodeCollection|null
     */
    public static function tryFromReflection(ReflectionClass|ReflectionMethod $reflection): AbstractRoutingTreeNode|RoutingTreeNodeCollection|null
    {
        try {
            return static::fromReflection($reflection);
            // because phpstan gives a dead catch false positive
            // due to early static binding (static::fromRef...)
            // TODO fix + pullrequest
        } catch (AttributeNotPresentException) {
            return null;
        }
    }
    /**
     * @param array<string> $array
     * @return string
     */
    final public static function pathFromArray(array $array): string
    {
        return implode('/', $array);
    }

    /**
     * @return array<int,string> $array
     */
    final public static function arrayFromPath(string $path): array
    {
        return explode('/', trim($path, "/"));
    }

    final public function getPath(): string
    {
        $pathStack = [];
        $pathStack[] = $this->getPathIdentifier();
        $parent = $this->getParent();
        while (null != $parent) {
            $pathStack[] = $parent->getPathIdentifier();
            $parent = $parent->getParent();
        }
        $pathStack = array_reverse($pathStack);
        return self::pathFromArray($pathStack);
    }

    final public function hasPath(string $path): bool
    {
        if ($this->getPath() === $path) {
            return true;
        }
        foreach ($this->children as $child) {
            if ($child->hasPath($path)) {
                return true;
            }
        }
        return false;
    }
    /**
     * Expects the path to contain the path identifier,
     * of the node that is being inserted
     */
    final public function insertAtPath(AbstractRoutingTreeNode $node, string $path, ?string $parentPath = null): bool
    {
        // if ($this->hasPath($path) && $node == $this->getByPath($path)) {
        if ($this->hasPath($path)) {
            return false;
        }
        if (null == $parentPath) {
            $pathArray = self::arrayFromPath($path);
            array_pop($pathArray);
            $parentPath = self::pathFromArray($pathArray);
        }
        if ($this->getPath() == $parentPath) {
            $this->addChild($node);
            return true;
        }
        foreach ($this->children as $child) {
            if ($child->insertAtPath($node, $path, $parentPath)) {
                return true;
            }
        }
        return false;
    }

    final public function getByPath(string $path): ?AbstractRoutingTreeNode
    {
        if ($this->getPath() == $path) {
            return $this;
        }
        foreach ($this->children as $child) {
            if (null != $child->getByPath($path)) {
                return $child->getByPath($path);
            }
        }
        return null;
    }


}
