<?php

namespace Gregs\AttributeRouter\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Middleware
{
    public function __construct(
        private string $middleware
    ) {
    }

    public function getMiddleware(): string
    {
        return $this->middleware;
    }
}
