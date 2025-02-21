<?php

namespace Gregs\AttributeRouter\Internal\DataStructures;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Iterator;
use Countable;
use Gregs\AttributeRouter\Exceptions\NotImplementedException;
use ReflectionClass;

/**
 * @implements Iterator<int,RoutingTreeNodeInterface>
 */
class RoutingTreeNodeCollection implements Iterator, Countable
{
    private int $index = 0;
    /**
     * @param array<int,RoutingTreeNodeInterface> $nodes
     */
    public function __construct(
        private array $nodes = []
    ) {
    }

    public function current(): RoutingTreeNodeInterface
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
     * @param RoutingTreeNodeInterface $node
     */
    public function append(RoutingTreeNodeInterface $node): void
    {
        $this->nodes[] = $node;
    }

    private function insertAtPath(RoutingTreeNodeInterface $node, string $path): bool
    {
        foreach ($this->nodes as $n) {
            if ($n->insertAtPath($node, $path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param RoutingTreeNodeInterface|RoutingTreeNodeCollection|null $node
     * @param array<string> $pathStack
     * @return void
     */
    private function insertByPathStack(RoutingTreeNodeInterface|RoutingTreeNodeCollection|null $node, array &$pathStack): void
    {
        if (null !== $node && $node instanceof RoutingTreeNodeInterface) {
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

    public static function fromNamespace(string $namespace): RoutingTreeNodeCollection
    {
        $classMap = ClassMapGenerator::createMap(app_path());
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
     * @throws NotImplementedException
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
