<?php

namespace App\Message;

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
