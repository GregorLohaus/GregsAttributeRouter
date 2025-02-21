<?php declare(strict_types=1);

namespace Gregs\AttributeRouter\Tests;

use Gregs\AttributeRouter\Attributes\Controller;
use Gregs\AttributeRouter\Attributes\Middleware;

#[Controller]
class WithControllerAttributeAndMethod {
    public function x() {}
}

#[Middleware('test')]
class WithMiddlewareAttributeAndMethod {
    public function x() {}
}

#[Middleware('foo')]
#[Middleware('bar')]
class WithMultipleMiddlewareAttributes {
}

#[Controller]
class WithControllerAttributeAndMiddlewareMethodAttribute {
    #[Middleware('test')]
    public function x(){}
}

#[Controller]
#[Controller]
class WithTwoControllerAttributes {
}

class EmptyClass {
}
