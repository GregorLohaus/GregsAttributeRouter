<?php declare(strict_types=1);

namespace Gregs\AttributeRouter\Tests;

use Exception;
use Gregs\AttributeRouter\Exceptions\ToManyAttributesPresentException;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeControllerNode;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeNodeCollection;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreePrefixNode;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Mockery;
use ReflectionClass;

#[UsesClass(RoutingTreeControllerNode::class)]
#[UsesClass(RoutingTreePrefixNode::class)]
#[UsesClass(AttributeNotPresentException::class)]
#[UsesClass(RoutingTreeNodeCollection::class)]
#[CoversClass(RoutingTreeControllerNode::class)]
final class RoutingTreeControllerNodeTest extends TestCase {
    public function testIsCreateableFromReflectionClass():void {
         $controllerNode = RoutingTreeControllerNode::fromReflection(new ReflectionClass(WithControllerAttributeAndMethod::class));
         $this->assertSame('gregs.attributerouter.tests.withcontrollerattributeandmethod', $controllerNode->getPathIdentifier());
    }
    public function testCanGetControllerNodePath():void {
        $prefixNode = new RoutingTreePrefixNode('test');
        $fooNode = new RoutingTreeControllerNode(WithControllerAttributeAndMethod::class);
        $prefixNode->addChild($fooNode);
        $this->assertSame("prefix.test/gregs.attributerouter.tests.withcontrollerattributeandmethod",$fooNode->getPath());
    }

    public function testFromReflectionThrowsAttributeNotPresentException():void {
        $this->expectException(AttributeNotPresentException::class);
        RoutingTreeControllerNode::fromReflection(new ReflectionClass(EmptyClass::class));
    }

    public function testTryFromReflectionReturnsNullOnMissingAttribute():void {
        $node = RoutingTreeControllerNode::tryFromReflection(new ReflectionClass(EmptyClass::class));
        $this->assertSame(null,$node);
    }
    
    public function testFromReflectionThrowsToManyAttributePresentException():void {
        $this->expectException(ToManyAttributesPresentException::class);
        RoutingTreeControllerNode::fromReflection(new ReflectionClass(WithTwoControllerAttributes::class));
    }

    public function testOnlyCreateableFromReflectionClass():void {
        $this->expectException(Exception::class);
        RoutingTreeControllerNode::fromReflection((new ReflectionClass(WithControllerAttributeAndMethod::class))->getMethod('x'));
    }

    public function testIsAllowedToBeRoot():void {
        $node = new RoutingTreeControllerNode(WithControllerAttributeAndMethod::class);
        $this->assertSame(true,$node->allowedRoot());
    }

    public function testInvokeCreatesRoute():void{
        $node = new RoutingTreeControllerNode(WithControllerAttributeAndMethod::class);
        // $expectation = Route::shouldReceive('controller')->with(Foo::class)->once()->andReturn(new RouteRegistrar(new Router(new Dispatcher())));
        $mockRegistrar = Mockery::mock(RouteRegistrar::class);
        $mockRegistrar->shouldReceive('group')->once()->withArgs(function($arg){
            $this->assertIsCallable($arg);
            $arg();
            return true;
        });
        Route::shouldReceive('controller')->with(WithControllerAttributeAndMethod::class)->once()->andReturn($mockRegistrar);
        $node();
    }
}
