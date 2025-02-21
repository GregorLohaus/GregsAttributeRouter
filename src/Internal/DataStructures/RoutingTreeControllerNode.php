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
class RoutingTreeControllerNode extends AbstractRoutingTreeNode implements RoutingTreeNodeInterface
{
    //@codeCoverageIgnoreStart
    public function __construct(
        private string $controllerClass,
        RoutingTreeNodeCollection $children = new RoutingTreeNodeCollection([]),
        ?RoutingTreeNodeInterface $parent = null,
    ) {
        parent::__construct($children, $parent);
    }
    //@codeCoverageIgnoreEnd
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

    public function allowedRoot(): bool
    {
        return true;
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return RoutingTreeNodeInterface|RoutingTreeNodeCollection
     * @throws Exception
     * @throws AttributeNotPresentException
     * @throws ToManyAttributesPresentException
     */
    public static function fromReflection(ReflectionClass|ReflectionMethod $reflection): RoutingTreeNodeInterface|RoutingTreeNodeCollection
    {
        if (!($reflection instanceof ReflectionClass)) {
            throw new Exception("Cannot create ". self::class ." from" . $reflection::class);
        }
        $attributes = $reflection->getAttributes(Controller::class);
        if (count($attributes) < 1) {
            throw new AttributeNotPresentException(Controller::class);
        }
        if (count($attributes) > 1) {
            throw new ToManyAttributesPresentException("Multiple controller attributes present on class");
        }
        return new self($reflection->getName());
    }
}
