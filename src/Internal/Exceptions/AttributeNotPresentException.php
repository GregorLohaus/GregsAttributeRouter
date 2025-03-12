<?php

declare(strict_types=1);

namespace Gregs\AttributeRouter\Internal\Exceptions;

use RuntimeException;
use Throwable;

class AttributeNotPresentException extends RuntimeException implements Throwable
{
    public function __construct(
        private string $expectedAttribute,
    ) {
    }

    public function __toString(): string
    {
        return "Attribute {$this->expectedAttribute} not present.";
    }
}
