<?php

declare(strict_types=1);
/**
 * @internal Internal namespace
 */

namespace Gregs\AttributeRouter\Internal\DataStructures;

use Gregs\AttributeRouter\Exceptions\NotImplementedException;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use ReflectionClass;
use ReflectionMethod;

abstract class AbstractRoutingTreeNode implements RoutingTreeNodeInterface
{
    protected function __construct(
        protected RoutingTreeNodeCollection $children = new RoutingTreeNodeCollection([]),
        protected ?RoutingTreeNodeInterface $parent = null,
    ) {
    }

    public function __invoke(): void
    {
        throw new NotImplementedException();
    }

    public function allowedRoot(): bool
    {
        throw new NotImplementedException();
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return RoutingTreeNodeInterface|RoutingTreeNodeCollection
     * @throws NotImplementedException
     */
    public static function fromReflection(ReflectionClass|ReflectionMethod $reflection): RoutingTreeNodeInterface|RoutingTreeNodeCollection
    {
        throw new NotImplementedException();
    }

    public function getPathIdentifier(): string
    {
        throw new NotImplementedException();
    }

    public function setParent(RoutingTreeNodeInterface $parent): RoutingTreeNodeInterface
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent(): ?RoutingTreeNodeInterface
    {
        return $this->parent;
    }

    public function addChild(RoutingTreeNodeInterface $child): RoutingTreeNodeInterface|false
    {
        $this->children->append($child->setParent($this));
        return $this;
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return RoutingTreeNodeInterface|RoutingTreeNodeCollection|null
     */
    public static function tryFromReflection(ReflectionClass|ReflectionMethod $reflection): RoutingTreeNodeInterface|RoutingTreeNodeCollection|null
    {
        try {
            return static::fromReflection($reflection);
            // because phpstan gives a dead catch false positive
            // due to early static binding (static::fromRef...)
            // TODO fix + pullrequest
            // @phpstan-ignore-next-line
        } catch (AttributeNotPresentException) {
            return null;
        }
    }
    /**
     * @param array<string> $array
     * @return string
     */
    public static function pathFromArray(array $array): string
    {
        return implode('/', $array);
    }

    /**
     * @return array<int,string> $array
     */
    public static function arrayFromPath(string $path): array
    {
        return explode('/', trim($path, "/"));
    }

    public function getPath(): string
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

    public function hasPath(string $path): bool
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
    public function insertAtPath(RoutingTreeNodeInterface $node, string $path, ?string $parentPath = null): bool
    {
        if ($this->hasPath($path)) {
            // throw new DuplicatePathException($path);
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

    public function getByPath(string $path): ?RoutingTreeNodeInterface
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
