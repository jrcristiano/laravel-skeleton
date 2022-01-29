<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    protected $model, $filter = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function paginate(int $perPage)
    {
        return $this->model->paginate($perPage);
    }

    public function fetchAll(array $filter, array $relations = [])
    {
        $query = $this->query($filter, $relations);
        return $filter['paginated'] ? $query->paginate($filter['perPage']) : $query->get();
    }

    private function query(array $filter, array $relations)
    {
        extract($filter);
        return $this->model->select($columns)
            ->when($sortBy,
                function ($query) use ($sortBy, $orderBy) {
                    if (strpos($sortBy, ',') === false) {
                        return $query->orderBy($sortBy, $orderBy);
                    }

                    $sortBy = explode(',', $sortBy);
                    foreach ($sortBy as $column) {
                        $query->orderBy($column, $orderBy);
                    }
        })
        ->when($offset && (isset($paginate) == false || $paginate == false),
            function ($query) use ($offset, $limit) {
                $query->skip($offset)->take($limit);
        })
        ->when($relations != [], function ($query) use ($relations) {
            foreach ($relations as $relation) {
                $query->with($relation);
            }
        });
    }

    public function first(array $relations = [])
    {
        return $this->model->when($relations != [],
            function ($query) use ($relations) {
                foreach ($relations as $relation) {
                    $query->with($relation);
                }
        })
        ->first();
    }

    public function findOrFail(int $id, array $relations = [], $trashed = false)
    {
        $query = $this->model
            ->when($relations != [], function ($query) use ($relations) {
                foreach ($relations as $relation) {
                    $query->with($relation);
                }
        });

        if ($trashed) {
            return $query->withTrashed()->findOrFail($id);
        }

        return $query->findOrFail($id);
    }

    public function find(int $id, array $relations = [], $trashed = false)
    {
        $query = $this->model
            ->when($relations != [], function ($query) use ($relations) {
                foreach ($relations as $relation) {
                    $query->with($relation);
                }
        });

        if ($trashed) {
            return $query->with($relations)
                ->withTrashed()
                ->find($id);
        }

        return $query->with($relations)
            ->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data)
    {
        $data['id'] = $data['id'] ?? null;
        return $this->model->find($data['id'])
            ->update($data);
    }

    public function updateOrCreate(array $data)
    {
        return $this->model->updateOrCreate($data);
    }

    public function firstOrCreate(array $data)
    {
        return $this->model->firstOrCreate($data);
    }

    public function delete(int $id)
    {
        return $this->model->findOrFail($id)
            ->delete();
    }

    public function forceDelete(int $id)
    {
        return $this->model->findOrFail($id)
            ->forceDelete();
    }

    public function restore(int $id)
    {
        return $this->model->findOrFail($id)
            ->restore();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getTableName()
    {
        return $this->model->getTable();
    }
}
