<?php

namespace Gregs\AttributeRouter\Tests;

use Gregs\AttributeRouter\Attributes\Prefix;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeNodeCollection;
use Gregs\AttributeRouter\Exceptions\ToManyAttributesPresentException;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreePrefixNode;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use Gregs\AttributeRouter\Tests\Data\BasicUse\BasicUse;
use Gregs\AttributeRouter\Tests\Data\FaultyPrefixUse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;


#[CoversClass(RoutingTreePrefixNode::class)]
#[CoversClass(Prefix::class)]
#[CoversClass(AttributeNotPresentException::class)]
#[CoversClass(ToManyAttributesPresentException::class)]
#[CoversClass(RoutingTreeNodeCollection::class)]
class PrefixNodeTest extends TestCase {
    public function testFromreflectionShouldThrowOnMissingPrefixAttribute(): void {
        $this->expectException(AttributeNotPresentException::class);
        RoutingTreePrefixNode::fromReflection(new ReflectionClass(self::class));
    }
    public function testFromreflectionShouldThrowOnMultiplePrefixAttributes(): void {
        $this->expectException(ToManyAttributesPresentException::class);
        RoutingTreePrefixNode::fromReflection(new ReflectionClass(FaultyPrefixUse::class));
    }
    public function testPrefixShouldBeAllowedRoot(): void {
        $prefixNode = RoutingTreePrefixNode::fromReflection(new ReflectionClass(BasicUse::class));
        $this->assertEquals(true, $prefixNode->allowedRoot());
    }
}
