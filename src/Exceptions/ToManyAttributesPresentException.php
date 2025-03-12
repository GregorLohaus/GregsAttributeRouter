<?php

declare(strict_types=1);

namespace Gregs\AttributeRouter\Exceptions;

use RuntimeException;
use Throwable;

class ToManyAttributesPresentException extends RuntimeException implements Throwable
{
    public function __construct(
        private string $targetClass,
        private string $reflectedClass
    ) {
    }

    public function __toString(): string
    {
        return "Multiple {$this->targetClass} attributes present on {$this->reflectedClass}";
    }
}
