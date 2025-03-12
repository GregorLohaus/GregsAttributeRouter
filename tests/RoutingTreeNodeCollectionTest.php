<?php

namespace Gregs\AttributeRouter\Tests;

use Gregs\AttributeRouter\Attributes\Controller;
use Gregs\AttributeRouter\Attributes\Delete;
use Gregs\AttributeRouter\Attributes\Get;
use Gregs\AttributeRouter\Attributes\Middleware;
use Gregs\AttributeRouter\Attributes\Options;
use Gregs\AttributeRouter\Attributes\Patch;
use Gregs\AttributeRouter\Attributes\Post;
use Gregs\AttributeRouter\Attributes\Prefix;
use Gregs\AttributeRouter\Attributes\Put;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeNodeCollection;
use Gregs\AttributeRouter\Internal\DataStructures\AbstractRoutingTreeNode;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeControllerNode;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeMiddlewareNode;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreePrefixNode;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeVerbNode;
use Illuminate\Container\Container;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionClass;
use ReflectionObject;

#[CoversClass(Middleware::class)]
#[CoversClass(Patch::class)]
#[CoversClass(AbstractRoutingTreeNode::class)]
#[CoversClass(Delete::class)]
#[CoversClass(Post::class)]
#[CoversClass(Controller::class)]
#[CoversClass(Put::class)]
#[CoversClass(Patch::class)]
#[CoversClass(Get::class)]
#[CoversClass(Options::class)]
#[CoversClass(Prefix::class)]
#[CoversClass(RoutingTreeControllerNode::class)]
#[CoversClass(RoutingTreeMiddlewareNode::class)]
#[CoversClass(RoutingTreeNodeCollection::class)]
#[CoversClass(RoutingTreePrefixNode::class)]
#[CoversClass(RoutingTreeVerbNode::class)]
#[CoversClass(AttributeNotPresentException::class)]
class RoutingTreeNodeCollectionTest extends MockeryTestCase {
    public function testKey(): void {
        $collection = new RoutingTreeNodeCollection([]);
        $this->assertEquals(0, $collection->key());
    }

    public function testCount(): void {
        $collection = new RoutingTreeNodeCollection([]);
        $this->assertEquals(0, $collection->count());
    }

    public function testFromNamspaceProducesStructurallySoundCollection(): void {
        $mockContainer = Mockery::mock(Container::class);
        Container::setInstance($mockContainer);
        $mockContainer->shouldReceive('path')->once()->andReturn(__DIR__);
        $collection = RoutingTreeNodeCollection::fromNamespace("Gregs\\AttributeRouter\\Tests\\Data\\BasicUse");
        $this->assertEquals(1, $collection->count());
        $rootNode = $collection->current();
        $this->assertEquals('prefix.test', $rootNode->getPath());
        $this->assertEquals(true, $rootNode->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse"));
        $controllerNode = $rootNode->getByPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse");
        $this->assertEquals(true, $controllerNode->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1"));
        $prefixV1Node = $controllerNode->getByPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1");
        $this->assertEquals(true, $prefixV1Node->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/middleware.auth-sanctum"));
        $this->assertEquals(true, $prefixV1Node->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/get.x.get"));
        $this->assertEquals(true, $prefixV1Node->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/options.x.options"));
        $this->assertEquals(false, $prefixV1Node->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/put.x.put"));
        $this->assertEquals(false, $prefixV1Node->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/post.x.post"));
        $this->assertEquals(false, $prefixV1Node->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/delete.x.delete"));
        $this->assertEquals(false, $prefixV1Node->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/patch.x.patch"));
        $authMiddlewareNode = $prefixV1Node->getByPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/middleware.auth-sanctum");
        $this->assertEquals(true, $authMiddlewareNode->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/middleware.auth-sanctum/put.x.put"));
        $this->assertEquals(true, $authMiddlewareNode->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/middleware.auth-sanctum/post.x.post"));
        $this->assertEquals(true, $authMiddlewareNode->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/middleware.auth-sanctum/delete.x.delete"));
        $this->assertEquals(true, $authMiddlewareNode->hasPath("prefix.test/gregs.attributerouter.tests.data.basicuse.basicuse/prefix.v1/middleware.auth-sanctum/patch.x.patch"));
    }

    public function testInsetByPathStackSupportsMultiRootNodeInsertion(): void {
        $collection = RoutingTreeNodeCollection::fromNamespace("Gregs\\AttributeRouter\\Tests\\Data", __DIR__ . "/Data/MultipleMiddlewares.php");
        $this->assertEquals("middleware.x", $collection->current()->getPath());
        $this->assertEquals(true, $collection->current()->hasPath("middleware.x/middleware.y"));
    }
}
