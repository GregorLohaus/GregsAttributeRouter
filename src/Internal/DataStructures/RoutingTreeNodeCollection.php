<?php

namespace Gregs\AttributeRouter\Internal\DataStructures;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Iterator;
use Countable;
use ReflectionClass;

/**
 * @implements Iterator<int,AbstractRoutingTreeNode >
 */
final class RoutingTreeNodeCollection implements Iterator, Countable
{
    /**
     * @param array<int,AbstractRoutingTreeNode > $nodes
     */
    public function __construct(
        private array $nodes = [],
        private int $index = 0
    ) {
    }

    public function current(): AbstractRoutingTreeNode
    {
        return $this->nodes[$this->index];
    }

    public function next(): void
    {
        $this->index += 1;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return ($this->index < count($this->nodes) && $this->index >= 0);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function count(): int
    {
        return count($this->nodes);
    }
    /**
     * @return void
     * @param AbstractRoutingTreeNode  $node
     */
    public function append(AbstractRoutingTreeNode  $node): void
    {
        $this->nodes[] = $node;
    }

    private function insertAtPath(AbstractRoutingTreeNode  $node, string $path): bool
    {
        foreach ($this->nodes as $n) {
            if ($n->insertAtPath($node, $path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param AbstractRoutingTreeNode |RoutingTreeNodeCollection|null $node
     * @param array<string> $pathStack
     * @return void
     */
    private function insertByPathStack(AbstractRoutingTreeNode |RoutingTreeNodeCollection|null $node, array &$pathStack): void
    {
        if (null !== $node && $node instanceof AbstractRoutingTreeNode) {
            if (count($pathStack) < 1) {
                $this->append($node);
                array_push($pathStack, $node->getPathIdentifier());
            } else {
                array_push($pathStack, $node->getPathIdentifier());
                $this->insertAtPath($node, AbstractRoutingTreeNode::pathFromArray($pathStack));
            }
        }
        if (null !== $node && $node instanceof RoutingTreeNodeCollection) {
            foreach ($node as $node) {
                if (count($pathStack) < 1) {
                    $this->append($node);
                    array_push($pathStack, $node->getPathIdentifier());
                } else {
                    array_push($pathStack, $node->getPathIdentifier());
                    $this->insertAtPath($node, AbstractRoutingTreeNode::pathFromArray($pathStack));
                }
            }
        }
    }

    public static function fromNamespace(string $namespace,string $path = null): RoutingTreeNodeCollection
    {
        if (null == $path) {
            $classMap = ClassMapGenerator::createMap(app_path());
        } else {
            $classMap = ClassMapGenerator::createMap($path);
        }
        $namespaceClassNames = array_filter(array_keys($classMap), function ($className) use ($namespace) {
            return (false !== strpos($className, $namespace));
        });
        $namespaceReflectionClasses = array_map(function ($className) {
            return new ReflectionClass($className);
        }, $namespaceClassNames);
        return self::fromRefelctionClasses($namespaceReflectionClasses);
    }

    /**
     * @param array<ReflectionClass<object>> $reflectionClasses
     * @return RoutingTreeNodeCollection
     */
    public static function fromRefelctionClasses(array $reflectionClasses): RoutingTreeNodeCollection
    {
        $collection = new self([]);
        foreach ($reflectionClasses as $reflection) {
            $pathStack = [];
            $middlewareNode = RoutingTreeMiddlewareNode::tryFromReflection($reflection);
            $prefixNode = RoutingTreePrefixNode::tryFromReflection($reflection);
            $controllerNode = RoutingTreeControllerNode::tryFromReflection($reflection);
            $collection->insertByPathStack($prefixNode, $pathStack);
            $collection->insertByPathStack($middlewareNode, $pathStack);
            $collection->insertByPathStack($controllerNode, $pathStack);
            foreach ($reflection->getMethods() as $method) {
                $pathSubStack = $pathStack;
                $middlewareNode = RoutingTreeMiddlewareNode::tryFromReflection($method);
                $prefixNode = RoutingTreePrefixNode::tryFromReflection($method);
                $verbNode = RoutingTreeVerbNode::tryFromReflection($method);
                $collection->insertByPathStack($prefixNode, $pathSubStack);
                $collection->insertByPathStack($middlewareNode, $pathSubStack);
                $collection->insertByPathStack($verbNode, $pathSubStack);
            }
        }
        return $collection;
    }
}
