<?php

declare(strict_types=1);


namespace App\Repository;


use App\Common\Dto\Pageable;
use App\Common\Dto\PageableResult;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function create(array $attributes): Model;

    public function findById(int $id): Model|null;

    public function findAll(Pageable $pageable): PageableResult;

    public function update(int $id, array $values): void;

    public function delete(int $id);
}
