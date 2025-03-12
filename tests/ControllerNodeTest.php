<?php

namespace Gregs\AttributeRouter\Tests;

use Exception;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeNodeCollection;
use Gregs\AttributeRouter\Exceptions\ToManyAttributesPresentException;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeControllerNode;
use Gregs\AttributeRouter\Tests\Data\BasicUse\BasicUse;
use Gregs\AttributeRouter\Tests\Data\FaultyControllerUse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

#[CoversClass(RoutingTreeControllerNode::class)]
#[CoversClass(AttributeNotPresentException::class)]
#[CoversClass(RoutingTreeNodeCollection::class)]
#[CoversClass(ToManyAttributesPresentException::class)]
class ControllerNodeTest extends TestCase {
    public function testFromreflectionShouldThrowOnReflectionmethodArg(): void {
        $this->expectException(Exception::class);
        RoutingTreeControllerNode::fromReflection(new ReflectionMethod(new BasicUse(),'get'));
    }
    public function testFromreflectionShouldThrowOnMissingControllerAttribute(): void {
        $this->expectException(AttributeNotPresentException::class);
        RoutingTreeControllerNode::fromReflection(new ReflectionClass(self::class));
    }
    public function testFromreflectionShouldThrowOnMultipleControllerAttributes(): void {
        $this->expectException(ToManyAttributesPresentException::class);
        RoutingTreeControllerNode::fromReflection(new ReflectionClass(FaultyControllerUse::class));
    }
    public function testControllerShouldBeAllowedRoot(): void {
        $controllerNode = RoutingTreeControllerNode::fromReflection(new ReflectionClass(BasicUse::class));
        $this->assertEquals(true, $controllerNode->allowedRoot());
    }
}
