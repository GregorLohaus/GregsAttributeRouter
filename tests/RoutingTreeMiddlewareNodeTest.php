<?php declare(strict_types=1);

namespace Gregs\AttributeRouter\Tests;

use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeMiddlewareNode;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeNodeCollection;
use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreePrefixNode;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use Gregs\AttributeRouter\Attributes\Middleware;
use Illuminatemiddleware\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Mockery;
use ReflectionClass;

#[UsesClass(RoutingTreeMiddlewareNode::class)]
#[UsesClass(RoutingTreePrefixNode::class)]
#[UsesClass(AttributeNotPresentException::class)]
#[UsesClass(Middleware::class)]
#[UsesClass(RoutingTreeNodeCollection::class)]
#[CoversClass(RoutingTreeMiddlewareNode::class)]
final class RoutingTreeMiddlewareNodeTest extends TestCase {
    public function testCanBeCreatedFromReflectionClass():void {
         $middlewareClassNode = RoutingTreeMiddlewareNode::fromReflection(new ReflectionClass(WithMiddlewareAttributeAndMethod::class));
         $middlewareMethodNode = RoutingTreeMiddlewareNode::fromReflection((new ReflectionClass(WithControllerAttributeAndMiddlewareMethodAttribute::class))->getMethod('x'));
         $this->assertSame('middleware.test', $middlewareClassNode->getPathIdentifier());
         $this->assertSame('middleware.test', $middlewareMethodNode->getPathIdentifier());
    }
    public function testCanGetPath():void {
        $prefixNode = new RoutingTreePrefixNode('test');
        $fooNode = new RoutingTreeMiddlewareNode('test');
        $prefixNode->addChild($fooNode);
        $this->assertSame("prefix.test/middleware.test",$fooNode->getPath());
    }

    public function testFromReflectionThrowsAttributeNotPresentException():void {
        $this->expectException(AttributeNotPresentException::class);
        RoutingTreeMiddlewareNode::fromReflection(new ReflectionClass(EmptyClass::class));
    }

    public function testTryFromReflectionReturnsNullOnMissingAttribute():void {
        $node = RoutingTreeMiddlewareNode::tryFromReflection(new ReflectionClass(EmptyClass::class));
        $this->assertSame(null,$node);
    }
    
    public function testCreatesNodeCollectionWhenMultipleAttributesPresent():void {
        $collection = RoutingTreeMiddlewareNode::fromReflection(new ReflectionClass(WithMultipleMiddlewareAttributes::class));
        $this->assertSame(count($collection),2);
    }

    public function testIsCreatableFromReflectionMethod():void {
        $node = RoutingTreeMiddlewareNode::fromReflection((new ReflectionClass(WithControllerAttributeAndMiddlewareMethodAttribute::class))->getMethod('x'));
        $this->assertSame('middleware.test',$node->getPathIdentifier());
    }

    public function testIsAllowedToBeRoot():void {
        $node = new RoutingTreeMiddlewareNode(WithControllerAttributeAndMethod::class);
        $this->assertSame(true,$node->allowedRoot());
    }

    public function testInvokeCreatesRoute():void{
        $node = new RoutingTreeMiddlewareNode('test');
        // $expectation = Route::shouldReceive('controller')->with(Foo::class)->once()->andReturn(new RouteRegistrar(new Router(new Dispatcher())));
        $mockRegistrar = Mockery::mock(RouteRegistrar::class);
        $mockRegistrar->shouldReceive('group')->once()->withArgs(function($arg){
            $this->assertIsCallable($arg);
            $arg();
            return true;
        });
        Route::shouldReceive('middleware')->with(['test'])->once()->andReturn($mockRegistrar);
        $node();
    }
}
