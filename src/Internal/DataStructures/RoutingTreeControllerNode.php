<?php

declare(strict_types=1);

namespace Gregs\AttributeRouter\Internal\DataStructures;

use Exception;
use Gregs\AttributeRouter\Attributes\Controller;
use Gregs\AttributeRouter\Exceptions\ToManyAttributesPresentException;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionMethod;

/**
 *   @property RoutingTreeNodeCollection $children
 */
class RoutingTreeControllerNode extends AbstractRoutingTreeNode
{
    protected function __construct(
        private string $controllerClass,
    ) {
        parent::__construct();
    }

    public function allowedRoot(): bool
    {
        return true;
    }

    public function __invoke(): void
    {
        Route::controller($this->controllerClass)->group(function () {
            foreach ($this->children as $child) {
                $child();
            }
        });
    }

    public function getPathIdentifier(): string
    {
        return strtolower(trim(str_replace("\\", ".", $this->controllerClass), "."));
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return AbstractRoutingTreeNode |RoutingTreeNodeCollection
     * @throws Exception
     * @throws AttributeNotPresentException
     * @throws ToManyAttributesPresentException
     */
    public static function fromReflection(ReflectionClass|ReflectionMethod $reflection): AbstractRoutingTreeNode |RoutingTreeNodeCollection
    {
        if (!($reflection instanceof ReflectionClass)) {
            throw new Exception("Cannot create ". self::class ." from" . $reflection::class);
        }
        $attributes = $reflection->getAttributes(Controller::class);
        if (count($attributes) < 1) {
            throw new AttributeNotPresentException(Controller::class);
        }
        if (count($attributes) > 1) {
            throw new ToManyAttributesPresentException(self::class, $reflection->getName());
        }
        return new self($reflection->getName());
    }

}
