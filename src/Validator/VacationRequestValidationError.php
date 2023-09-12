<?php

namespace App\Validator;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum VacationRequestValidationError implements TranslatableInterface
{
    case DatesOutOfSync;
    case ZeroDaysRequested;
    case NotEnoughtAvailableVacationDays;

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return match ($this) {
            self::DatesOutOfSync => $translator->trans('vacation_request.validator.dates_out_of_sync'),
            self::ZeroDaysRequested => $translator->trans('vacation_request.validator.zero_days_requested'),
            self::NotEnoughtAvailableVacationDays => $translator->trans('vacation_request.validator.not_enought_available_vacation_days'),
        };
    }
}
