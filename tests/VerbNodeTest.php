<?php

namespace Gregs\AttributeRouter\Tests;

use Exception;
use Gregs\AttributeRouter\Attributes\Get;
use Gregs\AttributeRouter\Attributes\Verb;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeNodeCollection;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeVerbNode;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use Gregs\AttributeRouter\Tests\Data\BasicUse\BasicUse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;


#[CoversClass(AttributeNotPresentException::class)]
#[CoversClass(RoutingTreeVerbNode::class)]
#[CoversClass(Verb::class)]
#[CoversClass(Get::class)]
#[CoversClass(RoutingTreeNodeCollection::class)]
class VerbNodeTest extends TestCase {
    public function testFromreflectionShouldThrowOnReflectionclassArg(): void {
        $this->expectException(Exception::class);
        RoutingTreeVerbNode::fromReflection(new ReflectionClass(self::class));
    }
    public function testFromreflectionShouldThrowOnMissingVerbAttribute(): void {
        $this->expectException(AttributeNotPresentException::class);
        RoutingTreeVerbNode::fromReflection(new ReflectionMethod(new BasicUse(),'noverb'));
    }
    public function testVerbShouldNotBeAllowedRoot(): void {
        $verbNodes = RoutingTreeVerbNode::fromReflection(new ReflectionMethod(new BasicUse(),'get'));
        $this->assertEquals(false, $verbNodes->current()->allowedRoot());
    }
}
