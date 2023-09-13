<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;

class RandomUserService
{
    /** @var Role[] $excludeRoles */
    protected array $excludedRoles = [];

    public function __construct(protected UserRepository $userRespository)
    {
    }

    public function excludeRole(string $role): void
    {
        $this->excludedRoles[] = $role;
        $this->excludedRoles = array_unique($this->excludedRoles);
    }

    public function getRandomUser(): User|null
    {
        $users = $this->userRespository->findAll();
        $filteredUsers = array_filter($users, function (User $user) {
            return empty(array_intersect($user->getRoles(), $this->excludedRoles));
        });
        if (empty($filteredUsers)) {
            return null;
        }

        $idx = array_rand($filteredUsers);
        return $users[$idx];
    }
}
