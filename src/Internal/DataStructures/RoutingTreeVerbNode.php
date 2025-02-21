<?php

declare(strict_types=1);

namespace Gregs\AttributeRouter\Internal\DataStructures;

use Exception;
use Illuminate\Support\Facades\Route;
use Gregs\AttributeRouter\Attributes\Verb;
use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

/**
 *   @property RoutingTreeNodeCollection $children
 */
class RoutingTreeVerbNode extends AbstractRoutingTreeNode implements RoutingTreeNodeInterface
{
    /**
     * @param value-of<Verb::VERBS> $verb
     * @param string $uri
     * @param string $action
     * @param RoutingTreeNodeCollection $children
     * @param null|RoutingTreeNodeInterface $parent
     * @return void
     */
    public function __construct(
        private string $verb,
        private string $uri,
        private string $action,
        RoutingTreeNodeCollection $children = new RoutingTreeNodeCollection([]),
        ?RoutingTreeNodeInterface $parent = null,
    ) {
        parent::__construct($children, $parent);
    }

    public function __invoke(): void
    {
        $verb = $this->verb;
        Route::$verb("/" . trim($this->uri, " \n\r\t\v\0/"), $this->action);
    }

    public function addChild(RoutingTreeNodeInterface $child): RoutingTreeNodeInterface|false
    {
        return false;
    }

    public function getPathIdentifier(): string
    {
        return $this->verb . '.' . str_replace("/", ".", trim($this->uri, " \n\r\t\v\0/")) . '.' . $this->action;
    }

    public function allowedRoot(): bool
    {
        return false;
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection
     * @return RoutingTreeNodeInterface|RoutingTreeNodeCollection
     * @throws Exception
     * @throws AttributeNotPresentException
     */
    public static function fromReflection(ReflectionClass|ReflectionMethod $reflection): RoutingTreeNodeInterface|RoutingTreeNodeCollection
    {
        if (!($reflection instanceof ReflectionMethod)) {
            throw new Exception("Cannot create ". self::class ." from" . $reflection::class);
        }
        $attributes = $reflection->getAttributes(Verb::class, ReflectionAttribute::IS_INSTANCEOF);
        if (count($attributes) < 1) {
            throw new AttributeNotPresentException(Verb::class);
        }
        $collection = new RoutingTreeNodeCollection();
        foreach ($attributes as $attribute) {
            $attributeInstance = $attribute->newInstance();
            $collection->append(new self($attributeInstance->getVerb(), $attributeInstance->getUri(), $reflection->getName()));
        }
        return $collection;
    }
}
