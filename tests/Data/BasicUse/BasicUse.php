<?php

namespace Gregs\AttributeRouter\Tests\Data\BasicUse;

use Gregs\AttributeRouter\Attributes\Controller;
use Gregs\AttributeRouter\Attributes\Delete;
use Gregs\AttributeRouter\Attributes\Get;
use Gregs\AttributeRouter\Attributes\Middleware;
use Gregs\AttributeRouter\Attributes\Options;
use Gregs\AttributeRouter\Attributes\Patch;
use Gregs\AttributeRouter\Attributes\Post;
use Gregs\AttributeRouter\Attributes\Prefix;
use Gregs\AttributeRouter\Attributes\Put;

#[Controller]
#[Prefix('test')]
class BasicUse {
    #[Get('x')]
    #[Prefix('v1')]
    public function get():void {
        
    }
    #[Options('x')]
    #[Prefix('v1')]
    public function options():void {
        
    }
    #[Patch('x')]
    #[Prefix('v1')]
    #[Middleware('auth-sanctum')]
    public function patch():void {
        
    }
    #[Put('x')]
    #[Prefix('v1')]
    #[Middleware('auth-sanctum')]
    public function put():void {
        
    }
    #[Post('x')]
    #[Prefix('v1')]
    #[Middleware('auth-sanctum')]
    public function post():void {
        
    }
    #[Delete('x')]
    #[Prefix('v1')]
    #[Middleware('auth-sanctum')]
    public function delete():void {
        
    }
    public function noverb():void {
        
    }
}
