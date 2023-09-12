<?php

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum VacationRequestStatus: string implements TranslatableInterface
{
    case Rejected = 'Rejected';
    case Approved = 'Approved';
    case PendingTeamLead = 'PendingTeamLead';
    case PendingProjectLead = 'PendingProjectLead';
    case Pending = 'Pending';

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return match ($this) {
            self::Rejected => $translator->trans('vacation_request.status.rejected'),
            self::Approved => $translator->trans('vacation_request.status.approved'),
            self::PendingTeamLead => $translator->trans('vacation_request.status.pending_team_lead'),
            self::PendingProjectLead => $translator->trans('vacation_request.status.pending_project_lead'),
            self::Pending => $translator->trans('vacation_request.status.pending'),
        };
    }
}
