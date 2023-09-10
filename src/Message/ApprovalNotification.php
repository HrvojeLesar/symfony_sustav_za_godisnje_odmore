<?php

namespace App\Message;

use App\Entity\VacationRequest;

class ApprovalNotification
{
    public function __construct(protected int $vacationRequestId)
    {
    }

    public function getVacationRequestId(): int
    {
        return $this->vacationRequestId;
    }
}
