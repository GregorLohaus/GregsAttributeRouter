<?php

namespace Gregs\AttributeRouter\Tests\Data;

use Gregs\AttributeRouter\Attributes\Middleware;

#[Middleware('x')]
#[Middleware('y')]
class MultipleMiddlewares {
    
}
