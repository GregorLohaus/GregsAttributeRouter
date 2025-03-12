<?php

namespace Gregs\AttributeRouter\Tests;

use Closure;
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
use Gregs\AttributeRouter\Tests\Data\BasicUse\BasicUse;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Gregs\AttributeRouter\AttributeRouter;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\RouteRegistrar;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass as PHPUnitCoversClass;
use RuntimeException;

use function PHPUnit\Framework\assertEquals;

#[PHPUnitCoversClass(AttributeRouter::class)]
#[PHPUnitCoversClass(Middleware::class)]
#[PHPUnitCoversClass(Patch::class)]
#[PHPUnitCoversClass(AbstractRoutingTreeNode::class)]
#[PHPUnitCoversClass(Delete::class)]
#[PHPUnitCoversClass(Post::class)]
#[PHPUnitCoversClass(Controller::class)]
#[PHPUnitCoversClass(Put::class)]
#[PHPUnitCoversClass(Patch::class)]
#[PHPUnitCoversClass(Get::class)]
#[PHPUnitCoversClass(Options::class)]
#[PHPUnitCoversClass(Prefix::class)]
#[PHPUnitCoversClass(RoutingTreeControllerNode::class)]
#[PHPUnitCoversClass(RoutingTreeMiddlewareNode::class)]
#[PHPUnitCoversClass(RoutingTreeNodeCollection::class)]
#[PHPUnitCoversClass(RoutingTreePrefixNode::class)]
#[PHPUnitCoversClass(RoutingTreeVerbNode::class)]
#[PHPUnitCoversClass(AttributeNotPresentException::class)]
class AttributeRouterTest extends MockeryTestCase {
    /**
     * @return void 
     * @throws RuntimeException 
     * @throws InvalidArgumentException 
     * @throws BindingResolutionException 
     */
    public function testStaticNamespaceMethodShouldWork():void {
        $mockRegistrar = Mockery::mock(RouteRegistrar::class);
        Route::shouldReceive('prefix')->once()->with('test')->andReturn($mockRegistrar);
        Route::shouldReceive('controller')->once()->with(BasicUse::class)->andReturn($mockRegistrar);
        Route::shouldReceive('prefix')->times(1)->with('v1')->andReturn($mockRegistrar);
        Route::shouldReceive('get')->once()->withArgs(function(...$args){
            assertEquals($args[0],'/x');
            assertEquals($args[1],'get');
            return true;
        })->andReturnUndefined();
        Route::shouldReceive('options')->once()->withArgs(function(...$args){
            assertEquals($args[0],'/x');
            assertEquals($args[1],'options');
            return true;
        });
        Route::shouldReceive('patch')->once()->withArgs(function(...$args){
            assertEquals($args[0],'/x');
            assertEquals($args[1],'patch');
            return true;
        });
        Route::shouldReceive('put')->once()->withArgs(function(...$args){
            assertEquals($args[0],'/x');
            assertEquals($args[1],'put');
            return true;
        });
        Route::shouldReceive('delete')->once()->withArgs(function(...$args){
            assertEquals($args[0],'/x');
            assertEquals($args[1],'delete');
            return true;
        });
        Route::shouldReceive('post')->once()->withArgs(function(...$args){
            assertEquals($args[0],'/x');
            assertEquals($args[1],'post');
            return true;
        });
        Route::shouldReceive('middleware')->times(1)->withArgs(function($args){
            assertEquals($args[0],'auth-sanctum');
            return true;
        })->andReturn($mockRegistrar);
        $mockRegistrar->shouldReceive('group')->times(4)->withArgs(function(Closure $arg) {
            $arg();
            return true;
        })->andReturn($mockRegistrar);
        AttributeRouter::namespace("Gregs\\AttributeRouter\\Tests\\Data", __DIR__ . "/Data/BasicUse/BasicUse.php");
    }
}
