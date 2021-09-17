<?php

declare(strict_types=1);


namespace App\Service\User;


use App\Common\Dto\CreateUserRequest;
use App\Common\Dto\Pageable;
use App\Common\Dto\PageableResult;
use App\Common\Dto\UpdateUserRequest;
use App\Common\Dto\User;

interface UserServiceInterface
{
    /**
     * Users pagination.
     *
     * @param Pageable $pageable
     * @return PageableResult
     */
    public function userList(Pageable $pageable): PageableResult;

    /**
     * Creates a new user.
     *
     * @param CreateUserRequest $request
     * @return User
     */
    public function registerUser(CreateUserRequest $request): User;

    /**
     * Updates user.
     *
     * @param int $userId
     * @param UpdateUserRequest $request
     * @return User
     */
    public function updateUser(int $userId, UpdateUserRequest $request): User;

    /**
     * Get user using id.
     *
     * @param int $userId
     * @return User
     */
    public function getUser(int $userId): User;

    /**
     * Deletes user.
     *
     * @param int $userId
     */
    public function deleteUser(int $userId): void;
}
