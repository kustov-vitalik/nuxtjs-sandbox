<?php

namespace App\Http\Controllers;

use App\Common\Dto\CreateUserRequest;
use App\Common\Dto\Pageable;
use App\Common\Dto\PageableResult;
use App\Common\Dto\UpdateUserRequest;
use App\Common\Dto\User;
use App\Service\Exception\BusyEmailException;
use App\Service\Exception\UserNotFoundException;
use App\Service\User\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends Controller
{
    private UserServiceInterface $userService;

    /**
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function listUsers(Pageable $pageable): PageableResult
    {
        return $this->userService->userList($pageable);
    }

    public function getUser(int $userId): User
    {
        try {
            return $this->userService->getUser($userId);
        } catch (UserNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }

    public function create(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->registerUser($request);

            return new JsonResponse($user, Response::HTTP_CREATED);
        } catch (BusyEmailException $e) {
            throw new ConflictHttpException($e->getMessage(), $e);
        }
    }

    public function update(int $userId, UpdateUserRequest $request): User
    {
        try {
            return $this->userService->updateUser($userId, $request);
        } catch (UserNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }

    public function delete(int $userId): JsonResponse
    {
        try {
            $this->userService->deleteUser($userId);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (UserNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }
}
