<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Find single model by id.
     *
     * @param int  $id
     * @param bool $findOrFail
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find(int $id, bool $findOrFail = true) : ?Model
    {
        return $findOrFail ?
            $this->model->newQuery()->findOrFail($id)
            : $this->model->newQuery()->find($id);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function findAll() : Collection
    {
        return $this->model->all();
    }

    /**
     * Find single model by given criteria.
     *
     * @param array $criteria
     *
     * @param bool  $firstOrFail
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findOneBy(array $criteria, bool $firstOrFail = true) : ?Model
    {
        $query = $this->applyCriteria($criteria);

        return $firstOrFail ? $query->firstOrFail() : $query->first();
    }

    /**
     * Find Many models by given criteria.
     *
     * @param array $criteria
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findManyBy(array $criteria) : null|Collection
    {
        $query = $this->applyCriteria($criteria);

        return $query->get();
    }

    public function paginateBy(array $criteria, int $from, int $offset) : Collection|array
    {
        $query = $this
            ->applyCriteria($criteria)
            ->orderByDesc('created_at')
            ->skip($from)
            ->take($offset);

        return $query->get();
    }

    /**
     * Apply given criteria to eloquent builder.
     *
     * @param array $criteria
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Builder
     */
    protected function applyCriteria(array $criteria)
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $criterion) {
            if (is_array($criterion[1])) {
                $query->whereIn(...$criterion);
            } else {
                $query->where(...$criterion);
            }
        }

        return $query;
    }

    /**
     * Find many models with given relations.
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllByWith(array $relations) : \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->model->newQuery();
        $query->with($relations);

        return $query->get();
    }
}
