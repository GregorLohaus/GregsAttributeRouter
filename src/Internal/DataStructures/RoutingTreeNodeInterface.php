<?php

declare(strict_types=1);

namespace Gregs\AttributeRouter\Internal\DataStructures;

use ReflectionMethod;
use ReflectionClass;

interface RoutingTreeNodeInterface
{
    public function __invoke(): void;
    public function allowedRoot(): bool;
    public function addChild(RoutingTreeNodeInterface $child): RoutingTreeNodeInterface|false;
    public function setParent(RoutingTreeNodeInterface $parent): RoutingTreeNodeInterface;
    public function getParent(): ?RoutingTreeNodeInterface;
    public function getPath(): string;
    public function hasPath(string $path): bool;
    public function getByPath(string $path): ?RoutingTreeNodeInterface;
    public function getPathIdentifier(): string;
    public function insertAtPath(RoutingTreeNodeInterface $node, string $path, ?string $parentPath = null): bool;
    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return RoutingTreeNodeInterface|RoutingTreeNodeCollection
     */
    public static function fromReflection(ReflectionClass|ReflectionMethod $reflection): RoutingTreeNodeInterface|RoutingTreeNodeCollection;
    /**
     * catches AttributeNotPresentException and returns null
     * because it doesnt indicate a usage error
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return RoutingTreeNodeInterface|RoutingTreeNodeCollection|null
     */
    public static function tryFromReflection(ReflectionClass|ReflectionMethod $reflection): RoutingTreeNodeInterface|RoutingTreeNodeCollection|null;
}
