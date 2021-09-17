<?php

declare(strict_types=1);


namespace App\Repository;

use App\Common\Dto\Pageable;
use App\Common\Dto\PageableResult;
use App\Models\User;

/**
 * @method User|null findById(int $id)
 * @method PageableResult|User[] findAll(Pageable $pageable)
 * @method User create(array $attributes)
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): User|null;
}
