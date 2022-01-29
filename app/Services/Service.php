<?php

namespace App\Services;

use App\Repositories\Repository;
use Exception;

abstract class Service
{
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function fetchAll(array $filter = [], array $relations = [])
    {
        $filters = $this->filters($filter);
        return $this->repository->fetchAll($filters, $relations);
    }

    public function first(array $relations = [])
    {
        return $this->repository->first($relations);
    }

    public function findOrFail(int $id, array $relations = [])
    {
        return $this->repository->findOrFail($id, $relations);
    }

    public function find(int $id, array $relations = [])
    {
        return $this->repository->find($id, $relations);
    }

    public function save(array $data)
    {
        $data['id'] = $data['id'] ?? null;
        if (!$data['id']) {
            return $this->create($data);
        }

        return $this->update($data);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(array $data)
    {
        return $this->repository->update($data);
    }

    public function updateOrCreate(array $data)
    {
        return $this->repository->updateOrCreate($data);
    }

    public function firstOrCreate(array $data)
    {
        return $this->repository->firstOrCreate($data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function forceDelete(int $id)
    {
        return $this->repository->forceDelete($id);
    }

    public function restore(int $id)
    {
        return $this->repository->restore($id);
    }

    public function getModel()
    {
        return $this->repository->getModel();
    }

    protected function filters(array $filter): array
    {
        $filter['columns'] = isset($filter['columns']) ? explode(',', $filter['columns']) : '*';
        $filter['orderBy'] = $filter['orderBy'] ?? 'desc';
        $filter['sortBy'] = $filter['sortBy'] ?? 'id';
        $filter['paginated'] = isset($filter['paginated']) && $filter['paginated'] == 'true' ? true : false;
        $filter['perPage'] = $filter['perPage'] ?? 25;
        $filter['limit'] = $filter['limit'] ?? 25;
        $filter['offset'] = $filter['offset'] ?? 0;
        return $filter;
    }
}
