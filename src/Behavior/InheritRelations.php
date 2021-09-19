<?php

namespace Tschallacka\StormInheritRelations\Behavior;

use Winter\Storm\Database\Model;
use Winter\Storm\Extension\ExtensionBase;

class InheritRelations extends ExtensionBase
{
    private static $cache = [];

    public function __construct($parent)
    {
        $key = get_class($parent);
        if ($parent instanceof Model && !isset(self::$cache[$key])) {

            $ancestors = class_parents($parent);
            $base_relations = $parent->getRelationDefinitions();

            foreach ($ancestors as $ancestor) {
                try {
                    $refl = new \ReflectionClass($ancestor);
                } catch (\ReflectionException $e) {
                    continue;
                }
                if($refl->isAbstract() || $refl->isInterface() || $refl->isTrait()) {
                    continue;
                }

                $instance = new $ancestor();
                if (method_exists($instance, 'getRelationDefinitions')) {
                    $relations = $instance->getRelationDefinitions();
                    $base_relations = array_merge_recursive($relations, $base_relations);
                }
            }
            self::$cache[$key] = $base_relations;
        }

        $relations = self::$cache[$key];
        foreach ($relations as $type => $defintions) {
            $parent->{$type} = $defintions;
        }
    }
}