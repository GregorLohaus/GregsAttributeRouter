<?php

namespace Gregs\AttributeRouter;

use Gregs\AttributeRouter\Internal\DataStructures\RoutingTreeNodeCollection;

class AttributeRouter
{
    /**
     * Namespace must be subnamespace on App\
     * and located in app_dir()
     * @param string $namespace
     * @return void
     */
    public static function namespace(string $namespace, string $path = null): void
    {
        $collection = RoutingTreeNodeCollection::fromNamespace($namespace, $path);
        foreach ($collection as $node) {
            $node();
        }
    }

}
