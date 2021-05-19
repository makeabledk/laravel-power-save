<?php

namespace Makeable\LaravelPowerSave\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Makeable\LaravelPowerSave\RelationRequest;

trait BuildsRelationships
{
    protected array $relations = [];

    protected bool $wantsAllRelations = false;

    protected bool $wantsAllNestedRelations = false;

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
        return $this->relations[$request->getRelationName()] ??= static::make($request->getRelatedModel())->withAllNested($this->wantsAllNestedRelations);
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

        return $attributes;
    }

    protected function wantsRelation($name): bool
    {
        return isset($this->relations[$name]) || $this->wantsAllRelations;
    }

    protected function saveBelongsTo()
    {
        collect($this->relations)
            ->filter($this->relationTypeIs(BelongsTo::class))
            ->each(function (self $builder, $relationName) {
                $builder->attributes === null
                    ? $this->newRelation($relationName)->disassociate()
                    : $this->newRelation($relationName)->associate($builder->save());
            });
    }

    protected function saveHasMany()
    {
        collect($this->relations)
            ->filter($this->relationTypeIs(HasOneOrMany::class))
            ->each(function (self $builder, $relationName) {
                $relation = $this->newRelation($relationName);
                $existing = $relation->get()->keyBy->getKey();
                $new = $builder->model->newCollection();

                foreach ($builder->attributes as $attributes) {
                    $builder
                        ->setModel($existing->get($attributes[$builder->model->getKeyName()] ?? null, $builder->model->newInstance()))
                        ->replaceAttributes($attributes);

                    // In case of morphOne / morphMany we'll need to set the morph type as well.
                    if ($relation instanceof MorphOneOrMany) {
                        $builder->fill([$relation->getMorphType() => $this->model->getMorphClass()]);
                    }

                    $builder->fill([$relation->getForeignKeyName() => $this->model->getKey()]);

                    tap($builder->save(), fn (Model $model) => $new->put($model->getKey(), $model));
                }

                $existing->except($new->keys()->all())->each->delete();

                $this->model->setRelation($relationName, $new->values());
            });
    }

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
}
