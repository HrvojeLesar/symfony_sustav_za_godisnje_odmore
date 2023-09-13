<?php

namespace App\Service;

use App\Entity\Role;

class RolesService
{
    /** @var string[] $roles */
    protected array $roles = [
        Role::User,
        Role::Admin,
        Role::TeamLead,
        Role::ProjectLead,
    ];

    public function excludeRole(string $role): void
    {
        $this->roles = array_filter($this->roles, function (string $existingRole) use ($role) {
            return $existingRole !== $role;
        });
    }

    public function getRandomRole(): string
    {
        $idx = array_rand($this->roles);
        return $this->roles[$idx];
    }
}
