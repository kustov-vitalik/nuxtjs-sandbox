<?php

declare(strict_types=1);


namespace App\Repository\Eloquent;


use App\Common\Dto\Direction;
use App\Common\Dto\Pageable;
use App\Common\Dto\PageableResult;
use App\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class Repository implements RepositoryInterface
{
    protected Model $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }


    public function create(array $attributes): Model
    {
        return $this->transaction(function () use ($attributes) {
            return $this->model->newQuery()->create($attributes);
        });
    }

    protected function transaction(callable $fn): mixed
    {
        return $this->model->getConnection()->transaction(function () use ($fn) {
            return $fn();
        });
    }

    public function findById(int $id): Model|null
    {
        return $this->model->newQuery()->find($id);
    }

    public function findAll(Pageable $pageable): PageableResult
    {
        $builder = $this->model->newQuery();
        $builder->from($this->model->getTable());

        $totalRows = $builder->count();

        $builder->skip($pageable->getOffset())
            ->take($pageable->getSize());

        foreach ($pageable->getSort() as $sortOrder) {
            switch ($sortOrder->getDirection()) {
                case Direction::ASC:
                    $builder->orderBy($sortOrder->getProperty());
                    break;
                case Direction::DESC:
                    $builder->orderByDesc($sortOrder->getProperty());
                    break;
            }
        }

        $pageRows = $builder->get()->all();

        return PageableResult::of($pageRows, $totalRows, $pageable);
    }

    public function update(int $id, array $values): void
    {
        $this->transaction(function () use ($id, $values) {
            $builder = $this->model->newQuery();
            $builder->whereKey($id)
                ->update($values);
        });
    }

    public function delete(int $id): void
    {
        $this->transaction(function () use ($id) {
            $builder = $this->model->newQuery();
            $builder->from($this->model->getTable())
                ->whereKey($id)
                ->delete();
        });

    }
}
