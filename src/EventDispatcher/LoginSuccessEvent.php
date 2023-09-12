<?php

namespace App\EventDispatcher;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class LoginSuccessEvent extends Event
{
    public function __construct(protected User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
