<?php

namespace Gregs\AttributeRouter\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Delete extends Verb
{
    public function __construct(string $uri)
    {
        parent::__construct('delete', $uri);
    }
}
