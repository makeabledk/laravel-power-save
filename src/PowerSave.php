<?php

namespace Makeable\LaravelPowerSave;

use Illuminate\Database\Eloquent\Model;
use Makeable\LaravelPowerSave\Concerns\BuildsRelationships;

class PowerSave
{
    use BuildsRelationships;

    protected Model $model;

    protected ?array $attributes = [];

    protected $fillFn;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->fillFn = fn (Model $model, array $attributes) => $model->fill($attributes);
    }

    public static function make($model): self
    {
        return new static(is_string($model) ? new $model : $model);
    }

    public function fill(?array $attributes): self
    {
        $this->attributes = array_merge($this->attributes ?? [], $attributes ?? []);

        if ($attributes === null) {
            $this->attributes = null;
        }

        return $this;
    }

    public function fillUsing(callable $fn): self
    {
        $this->fillFn = $fn;

        return $this;
    }

    public function inheritConfiguration(self $other): self
    {
        $this->wantsAllRelations = $other->wantsAllRelations;
        $this->wantsAllNestedRelations = $other->wantsAllNestedRelations;
        $this->fillFn = $other->fillFn;

        foreach ($other->relations as $name => $relation) {
            $this->relations[$name] = self::make($relation->model)->inheritConfiguration($relation);
        }

        return $this;
    }

    public function replaceAttributes(?array $attributes = []): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function with($name, callable $policy = null): self
    {
        return $this->prepareRelation(new RelationRequest($this->model, $name, $policy));
    }

    public function withAll($bool = true): self
    {
        $this->wantsAllRelations = $bool;

        return $this;
    }

    public function withAllNested($bool = true): self
    {
        $this->wantsAllRelations = $bool;
        $this->wantsAllNestedRelations = $bool;

        return $this;
    }

    public function save(array $data = []): Model
    {
        $this
            ->fill($data)
            ->prepareModelForSave()
            ->saveBelongsTo();

        $this->model->save();

        $this->saveHasMany();

        return $this->model;
    }

    protected function prepareModelForSave(): self
    {
        $key = $this->attributes[$this->model->getKeyName()] ?? null;

        if (! $this->model->exists && ! is_null($key)) {
            $this->model = $this->model->findOrFail($key);
        }

        call_user_func($this->fillFn, $this->model, $this->extractRelations($this->attributes));

        return $this;
    }
}
