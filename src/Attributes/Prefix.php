<?php

namespace Gregs\AttributeRouter\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Prefix
{
    public function __construct(
        private string $prefix
    ) {
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
