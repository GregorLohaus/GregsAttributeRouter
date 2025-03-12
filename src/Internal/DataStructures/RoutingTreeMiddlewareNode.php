<?php

declare(strict_types=1);

namespace Gregs\AttributeRouter\Internal\DataStructures;

use Gregs\AttributeRouter\Attributes\Middleware;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionMethod;

class RoutingTreeMiddlewareNode extends AbstractRoutingTreeNode
{
    protected function __construct(
        private string $middlewareName,
    ) {
        parent::__construct();
    }

    public function __invoke(): void
    {
        Route::middleware([$this->middlewareName])->group(function () {
            foreach ($this->children as $child) {
                $child();
            }
        });
    }

    public function getPathIdentifier(): string
    {
        return strtolower("middleware." . $this->middlewareName);
    }

    public function allowedRoot(): bool
    {
        return true;
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return AbstractRoutingTreeNode |RoutingTreeNodeCollection
     * @throws AttributeNotPresentException
     */
    public static function fromReflection(ReflectionClass|ReflectionMethod $reflection): AbstractRoutingTreeNode |RoutingTreeNodeCollection
    {
        $attributes = $reflection->getAttributes(Middleware::class);
        if (count($attributes) < 1) {
            throw new AttributeNotPresentException(Middleware::class);
        }
        if (count($attributes) < 2) {
            return new self($attributes[0]->newInstance()->getMiddleware());
        }
        $collection = new RoutingTreeNodeCollection();
        foreach ($attributes as $attribute) {
            $collection->append(new self($attribute->newInstance()->getMiddleware()));
        }
        return $collection;
    }
}
