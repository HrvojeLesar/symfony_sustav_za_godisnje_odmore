<?php

namespace App\Entity;

abstract class Role
{
    public const User = 'ROLE_USER';
    public const ProjectLead = 'ROLE_PROJECT_LEAD';
    public const TeamLead = 'ROLE_TEAM_LEAD';
    public const Admin = 'ROLE_ADMIN';

    /**
     * @return Role[]
     */
    public static function roleChoices(): array
    {
        return [
            'User' => Role::User,
            'Project Lead' => Role::ProjectLead,
            'Team Lead' => Role::TeamLead,
            'Admin' => Role::Admin,
        ];
    }
}
