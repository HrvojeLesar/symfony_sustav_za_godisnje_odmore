<?php

namespace App\Exceptions;

use Exception;

class InvalidPermissionsException extends Exception
{
    protected $message = 'User has no permissions for this action.';
}
