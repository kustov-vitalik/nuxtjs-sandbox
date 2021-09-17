<?php

declare(strict_types=1);


namespace App\Repository\Eloquent;


use App\Models\User;
use App\Repository\UserRepositoryInterface;

class UserRepository extends Repository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): User|null
    {
        $builder = $this->model->newQuery();
        $result = $builder->from($this->model->getTable())
            ->firstWhere('email', '=', $email);
        if ($result instanceof User) {
            return $result;
        }

        return null;
    }
}
