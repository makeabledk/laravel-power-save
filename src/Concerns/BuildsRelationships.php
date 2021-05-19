<?php

namespace Makeable\LaravelPowerSave\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Makeable\LaravelPowerSave\PowerSave;
use Makeable\LaravelPowerSave\RelationBuilder;
use Makeable\LaravelPowerSave\RelationRequest;

trait BuildsRelationships
{
    protected array $relations = [];

    protected function prepareRelation(RelationRequest $request): self
    {
        $builder = $this->createRelatedBuilder($request);

        $request->hasNesting()
            ? $builder->prepareRelation($request->createNestedRequest())
            : tap($builder, $request->policy);

        return $this;
    }

    protected function createRelatedBuilder(RelationRequest $request): self
    {
        return $this->relations[$request->getRelationName()] ??= static::make($request->getRelatedModel());
    }

    protected function extractRelations(array $attributes): array
    {
        foreach ($attributes as $attribute => $value) {
            if ($this->wantsRelation($attribute) && ($request = RelationRequest::make($this->model, $attribute))->isValidRelation()) {
                // Create related builder in case "wildcard *" relations was applied.
                $this->createRelatedBuilder($request)->fill($value);

                unset($attributes[$attribute]);
            }
        }

        dump('return attributes', $attributes);

        return $attributes;
    }

//    protected function makeRelation($name): ? Relation
//    {
//        if (method_exists($this->model, $name) && ($relation = $this->model->$name()) instanceof Relation) {
//            return $relation;
//        }
//
//        return null;
//    }
//
    protected function wantsRelation($name): bool
    {
        return isset($this->relations[$name]) || isset($this->relations['*']);
    }

    protected function saveBelongsTo()
    {
        collect($this->relations)
            ->filter($this->relationTypeIs(BelongsTo::class))
            ->each(function (self $builder, $relation) {
                $builder->attributes === null
                    ? $this->newRelation($relation)->disassociate()
                    : $this->newRelation($relation)->associate($builder->save());
            });
    }


//
//    /**
//     * Create all requested BelongsToMany relations.
//     *
//     * @param Model $sibling
//     */
//    protected function createBelongsToMany($sibling)
//    {
//        collect($this->relations)
//            ->filter($this->relationTypeIs(BelongsToMany::class))
//            ->each(function ($batches, $relation) use ($sibling) {
//                foreach ($batches as $factory) {
//                    $this
//                        ->collect($factory->inheritConnection($this)->create())
//                        ->map(function ($model) use ($sibling, $relation, $factory) {
//                            return $sibling->$relation()->save($model, $this->mergeAndExpandAttributes($factory->pivotAttributes));
//                        });
//                }
//            });
//    }
//
//    /**
//     * Create all requested HasMany relations.
//     *
//     * @param Model $parent
//     */
//    protected function createHasMany($parent)
//    {
//        collect($this->relations)
//            ->filter($this->relationTypeIs(HasOneOrMany::class))
//            ->each(function ($batches, $relation) use ($parent) {
//                foreach ($batches as $factory) {
//                    // In case of morphOne / morphMany we'll need to set the morph type as well.
//                    if (($relationClass = $this->newRelation($relation)) instanceof MorphOneOrMany) {
//                        $factory->fill([
//                            $relationClass->getMorphType() => (new $this->class)->getMorphClass(),
//                        ]);
//                    }
//
//                    $factory->inheritConnection($this)->create([
//                        $parent->$relation()->getForeignKeyName() => $parent->$relation()->getParentKey(),
//                    ]);
//                }
//            });
//    }
//
    protected function relationTypeIs($relationType): Closure
    {
        return function (self $builder, $relation) use ($relationType) {
            return $this->newRelation($relation) instanceof $relationType;
        };
    }

    protected function newRelation($relationName): Relation
    {
        return $this->model->$relationName();
    }
//
//    protected function inheritConnection($factory)
//    {
//        if ($this->connection === null && (new $this->class)->getConnectionName() === null) {
//            return $this->connection($factory->connection);
//        }
//    }
}
