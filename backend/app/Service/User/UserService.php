<?php

declare(strict_types=1);


namespace App\Service\User;


use App\Common\Dto\CreateUserRequest;
use App\Common\Dto\Pageable;
use App\Common\Dto\PageableResult;
use App\Common\Dto\UpdateUserRequest;
use App\Common\Dto\User as UserDto;
use App\Models\User;
use App\Repository\UserRepositoryInterface;
use App\Service\Exception\BusyEmailException;
use App\Service\Exception\UserNotFoundException;
use App\Service\Mapper\UserMapper;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private UserMapper $userMapper;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param UserMapper $userMapper
     */
    public function __construct(UserRepositoryInterface $userRepository, UserMapper $userMapper)
    {
        $this->userRepository = $userRepository;
        $this->userMapper = $userMapper;
    }


    public function userList(Pageable $pageable): PageableResult
    {
        $pageableResult = $this->userRepository->findAll($pageable);

        foreach ($pageableResult as $k => $user) {
            $pageableResult[$k] = $this->userMapper->toUserDto($user);
        }

        return $pageableResult;
    }

    public function registerUser(CreateUserRequest $request): UserDto
    {
        $existingUser = $this->userRepository->findByEmail($request->getEmail());
        if ($existingUser) {
            throw new BusyEmailException($request->getEmail());
        }

        $userEntity = $this->userRepository->create([
            'name' => $request->getName(),
            'password' => Hash::make($request->getPassword()),
            'email' => $request->getEmail(),
        ]);

        return $this->userMapper->toUserDto($userEntity);
    }

    public function updateUser(int $userId, UpdateUserRequest $request): UserDto
    {
        $user = $this->getUserOrThrow($userId);

        $this->userRepository->update($user->id, [
            'name' => $request->getName(),
        ]);

        return $this->getUser($userId);
    }

    private function getUserOrThrow(int $userId): User
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        return $user;
    }

    public function getUser(int $userId): UserDto
    {
        return $this->userMapper->toUserDto($this->getUserOrThrow($userId));
    }

    public function deleteUser(int $userId): void
    {
        $this->userRepository->delete($this->getUserOrThrow($userId)->id);
    }
}
