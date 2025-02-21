<?php

declare(strict_types=1);

namespace Gregs\AttributeRouter\Exceptions;

use RuntimeException;
use Throwable;

class NotImplementedException extends RuntimeException implements Throwable
{
    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null,
        private string $class = "",
        private string $function = ""
    ) {
        parent::__construct($message, $code, $previous);
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $this->class = $backtrace[1]['class'] ?? 'UnknownClass';
        $this->function = $backtrace[1]['function'] ?? 'UnknownFunction';
    }

    public function __toString(): string
    {
        return "$this->function not implemented in class $this->class";
    }
}
