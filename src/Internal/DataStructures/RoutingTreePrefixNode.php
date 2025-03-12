<?php

declare(strict_types=1);

namespace Gregs\AttributeRouter\Internal\DataStructures;

use Gregs\AttributeRouter\Attributes\Prefix;
use Gregs\AttributeRouter\Exceptions\ToManyAttributesPresentException;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionMethod;

/**
 *   @internal
 *   @property RoutingTreeNodeCollection $children
 */
class RoutingTreePrefixNode extends AbstractRoutingTreeNode
{
    protected function __construct(
        private string $prefix,
    ) {
        parent::__construct();
    }

    public function __invoke(): void
    {
        Route::prefix(trim($this->prefix, " \n\r\t\v\0/"))->group(function () {
            foreach ($this->children as $child) {
                $child();
            }
        });
    }

    public function getPathIdentifier(): string
    {
        return strtolower("prefix." . str_replace("/", ".", trim($this->prefix, " \n\r\t\v\0/")));
    }

    public function allowedRoot(): bool
    {
        return true;
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return AbstractRoutingTreeNode|RoutingTreeNodeCollection
     * @throws AttributeNotPresentException
     * @throws ToManyAttributesPresentException
     */
    public static function fromReflection(ReflectionClass|ReflectionMethod $reflection): AbstractRoutingTreeNode|RoutingTreeNodeCollection
    {
        $attributes = $reflection->getAttributes(Prefix::class);
        if (count($attributes) < 1) {
            throw new AttributeNotPresentException(Prefix::class);
        }
        if (count($attributes) > 1) {
            throw new ToManyAttributesPresentException(self::class,$reflection->getName());
        }
        return new self($attributes[0]->newInstance()->getPrefix());
    }
}
