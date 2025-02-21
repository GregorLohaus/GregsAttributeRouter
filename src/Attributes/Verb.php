<?php

namespace Gregs\AttributeRouter\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Verb
{
    public const GET = 'get';
    public const POST = 'post';
    public const PUT = 'put';
    public const PATCH = 'patch';
    public const DELETE = 'delete';
    public const OPTIONS = 'options';

    public const VERBS = [
        'get',
        'post',
        'put',
        'patch',
        'delete',
        'options'
    ];

    /**
     * @param value-of<Verb::VERBS> $verb
     * @return void
     */
    public function __construct(
        private string $verb,
        private string $uri
    ) {
    }

    /** @return value-of<self::VERBS>  */
    public function getVerb(): string
    {
        return $this->verb;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
