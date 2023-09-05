<?php

namespace App\Entity;

abstract class VacationRequestStatus
{
    public const Rejected = 'Zahtjev odbijen.';
    public const Approved = 'Zahtjev odobren.';
    public const PendingTeamLead = 'Čekanje na odobrenje voditelja tima.';
    public const PendingProjectLead = 'Čekanje na odobrenje voditelja projekta.';
    public const Pending = 'Čekanje odobrenja.';
}
