<?php

namespace App\Exceptions;

use Exception;

class NotRemovableException extends Exception
{
    protected $message = 'Selected VacationRequest is not removable';
}
