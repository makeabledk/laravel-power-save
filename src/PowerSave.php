<?php

namespace Makeable\LaravelPowerSave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Makeable\LaravelPowerSave\Concerns\BuildsRelationships;
use function class_basename;
use function collect;
use function dump;

class PowerSave
{
    use BuildsRelationships;

    protected Model $model;

    protected ? array $attributes = [];

    protected $fillFn;

//    protected array $pendingRelations = [];
//
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->fillFn = fn (Model $model, array $attributes) => $model->fill($attributes);
    }

    public static function make($model): self
    {
        return new static(is_string($model) ? new $model : $model);
    }

    public function fill(? array $attributes): self
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

    public function with($name, callable $policy = null): self
    {
        return $this->prepareRelation(new RelationRequest($this->model, $name, $policy));
    }

    public function withAll(): self
    {
        return $this->with('*');
    }

    public function save(array $data = []): Model
    {

//        foreach ($data as $attribute => $value) {
////            if ($this->wantsRelation($attribute) && ($relation = $this->makeRelation($attribute))) {
////                $this->handleRelation($relation, $attribute, $value);
////                continue;
////            }
//
//            $this->model->$attribute = $value;
//        }

//        $fillFn = $this->fillFn ?? fn (Model $model, array $attributes) => $model->fill($attributes);

        $this
            ->fill($data)
            ->prepareModelForSave()
            ->saveBelongsTo();

        $this->model->save();

//        $this->handlePendingRelations();

        return $this->model;
    }

    protected function prepareModelForSave(): self
    {
        $key = $this->attributes[$this->model->getKeyName()] ?? null;

        if (! $this->model->exists && ! is_null($key)) {
            $this->model = $this->model->newQuery()->firstOrFail($key);
        }

        call_user_func($this->fillFn, $this->model, $this->extractRelations($this->attributes));

        return $this;
    }

//
//    protected function handleRelation(Relation $relation, $name, $data)
//    {
//        if ($relation instanceof BelongsTo) {
//            $relation->associate(static::make($this->model->$name ?? $relation->getModel())->save($data));
//        } elseif ($relation instanceof HasMany) {
//            $this->pendingRelations[$name] = $data;
//        } else {
//            throw new \BadMethodCallException('Currently unsupported relation type: '.class_basename($relation));
//        }
//    }
//
//    protected function handlePendingRelations()
//    {
//        foreach ($this->pendingRelations as $relation => $data) {
//            $this->syncHasMany($relation, $data);
//        }
//    }
//
//    protected function syncHasMany($name, array $data)
//    {
//        $updated = collect();
//        $existing = $this->model->$name->keyBy->getKey();
//        $relation = $this->makeRelation($name);
//
//        foreach ($data as $modelData) {
//            $id = $modelData[$this->model->getKeyName()] ?? null;
//
//            if ($id) {
//                if ($existing->has($id)) {
//                    $updated->put($id, static::make($existing->get($id))->save($modelData));
//                }
//            } else {
//                static::make($relation->make())->save($modelData);
//            }
//        }
//
//        $existing->except($updated->keys()->all())->each->delete();
//
//        $this->model->unsetRelation($name);
//    }
}