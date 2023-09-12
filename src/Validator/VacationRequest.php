<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Attribute]
class VacationRequest extends Constraint
{
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getViolationString(VacationRequestValidationError $validationError, TranslatorInterface $translator): string
    {
        return $validationError->trans($translator);
    }
}
