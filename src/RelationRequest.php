<?php

namespace Makeable\LaravelPowerSave;

use BadMethodCallException;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class RelationRequest
{
    public Model $model;

    public string $path;

    public $policy;

    public function __construct(Model $model, $path, callable $policy = null)
    {
        $this->model = $model;
        $this->path = $path;
        $this->policy = $policy;
    }

    public static function make(...$args): self
    {
        return new static(...$args);
    }

    public function createNestedRequest(): self
    {
        return new static(
            $this->getRelatedModel(),
            $this->getNestedPath(),
            $this->policy
        );
    }

    public function getNestedPath($path = null): string
    {
        $nested = explode('.', $path ?: $this->path);

        array_shift($nested);

        return implode('.', $nested);
    }

    protected Model $cachedRelatedModel;

    public function getRelatedModel(): Model
    {
        $relation = $this->getRelationName();

        return $this->cachedRelatedModel ??= $this->model->$relation()->getModel();
    }

    public function getRelationName($path = null): string
    {
        $nested = explode('.', $path ?: $this->path);

        return array_shift($nested);
    }

    public function hasNesting(): bool
    {
        return strpos($this->path, '.') !== false;
    }

    public function isValidRelation($path = null): bool
    {
        $relation = $this->getRelationName($path);

        return method_exists($this->model, $relation) && $this->model->$relation() instanceof Relation;
    }

//    /**
//     * @return Model
//     */
//    protected function model()
//    {
//        return new $this->class;
//    }
//
//    /**
//     * Fail build with a readable exception message.
//     *
//     * @param Collection $args
//     */
//    protected function failOnMissingRelation(Collection $args)
//    {
//        if (! $this->path) {
//            throw new BadMethodCallException(
//                'No matching relations could be found on model ['.$this->class.']. '.
//                'Following possible relation names was checked: '.
//                (
//                    ($testedRelations = $this->getPossiblyIntendedRelationships($args))->isEmpty()
//                        ? '[NO POSSIBLE RELATION NAMES FOUND]'
//                        : '['.$testedRelations->implode(', ').']'
//                )
//            );
//        }
//    }
//
//    /**
//     * Give the developer a readable list of possibly args
//     * that they might have intended could be a relation,
//     * but was invalid. Helpful for debugging purposes.
//     *
//     * @param Collection $args
//     * @return string
//     */
//    protected function getPossiblyIntendedRelationships(Collection $args)
//    {
//        return $args
//            ->filter(function ($arg) {
//                return is_string($arg) || is_null($arg);
//            })
//            ->map(function ($arg) {
//                return is_null($arg) ? 'NULL' : "'".$arg."'";
//            });
//    }
}
