<?php

namespace Gregs\AttributeRouter\Tests;

use Gregs\AttributeRouter\Attributes\Middleware;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeNodeCollection;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeMiddlewareNode;
use Gregs\AttributeRouter\Tests\Data\BasicUse\BasicUse;
use Gregs\AttributeRouter\Tests\Data\MultipleMiddlewares;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

#[CoversClass(Middleware::class)]
#[CoversClass(RoutingTreeMiddlewareNode::class)]
#[CoversClass(RoutingTreeNodeCollection::class)]
class MiddlewareNodeTest extends TestCase {
    public function testMiddlewareShouldBeAllowedRoot(): void {
        $middlewareNode = RoutingTreeMiddlewareNode::fromReflection(new ReflectionMethod(new BasicUse,'post'));
        $this->assertEquals(true, $middlewareNode->allowedRoot());
    }

    public function testMiddlewareFromReflectionShouldSupportMultipleAttributes(): void {
        $middlewareNodes = RoutingTreeMiddlewareNode::fromReflection(new ReflectionClass(MultipleMiddlewares::class));
        $this->assertInstanceOf(RoutingTreeMiddlewareNode::class, $middlewareNodes->current());
        $middlewareNodes->next();
        $this->assertInstanceOf(RoutingTreeMiddlewareNode::class, $middlewareNodes->current());
    }
}
