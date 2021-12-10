<?php

namespace Barryvdh\LaravelIdeHelper\Console;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Relations\Relation;

class BaseModelsCommand extends ModelsCommand
{
    /**
     * Check if the relation is nullable
     *
     * @param string $relation
     * @param Relation $relationObj
     *
     * @return bool
     */
    protected function isRelationNullable(string $relation, Relation $relationObj): bool
    {
        $reflectionObj = new \ReflectionObject($relationObj);

        if (in_array($relation, ['hasOne', 'hasOneThrough', 'morphOne'], true) || !$reflectionObj->hasProperty('foreignKey')) {
            return parent::isRelationNullable($relation, $relationObj);
        }

        $fkProp = $reflectionObj->getProperty('foreignKey');
        $fkProp->setAccessible(true);

        return (bool) Arr::first(
            (array) $fkProp->getValue($relationObj),
            fn (string $value) => isset($this->nullableColumns[$value])
        );
    }
}
