<?php

declare(strict_types=1);

namespace Gregs\AttributeRouter\Exceptions;

use RuntimeException;
use Throwable;

class DuplicatePathException extends RuntimeException implements Throwable
{
    public function __construct(
        private string $path,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return "Duplicate path: {$this->path}";
    }
}
