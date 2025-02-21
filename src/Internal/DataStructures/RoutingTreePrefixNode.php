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
class RoutingTreePrefixNode extends AbstractRoutingTreeNode implements RoutingTreeNodeInterface
{
    public function __construct(
        private string $prefix,
        RoutingTreeNodeCollection $children = new RoutingTreeNodeCollection([]),
        ?RoutingTreeNodeInterface $parent = null,
    ) {
        parent::__construct($children, $parent);
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
     * @return RoutingTreeNodeInterface|RoutingTreeNodeCollection
     * @throws AttributeNotPresentException
     * @throws ToManyAttributesPresentException
     */
    public static function fromReflection(ReflectionClass|ReflectionMethod $reflection): RoutingTreeNodeInterface|RoutingTreeNodeCollection
    {
        $attributes = $reflection->getAttributes(Prefix::class);
        if (count($attributes) < 1) {
            throw new AttributeNotPresentException(Prefix::class);
        }
        if (count($attributes) > 1) {
            throw new ToManyAttributesPresentException("Multiple prefix attributes present on" . $reflection->getName());
        }
        return new self($attributes[0]->newInstance()->getPrefix());
    }
}
