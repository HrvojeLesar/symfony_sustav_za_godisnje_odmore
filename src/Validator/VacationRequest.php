<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class VacationRequest extends Constraint
{
    public $datesOutOfSync = 'Datum "Do" ima vrijednost koja je ranije nego datum "Od".';
    public $zeroDaysRequested = 'Potrebno je zatražiti minimalno 1 dan godišnjeg odmora.';
    public $notEnoughAvailableVacationDays = 'Zatraženo ({{ requested }}) je više dana godišnjeg odmora nego je dopušteno. Preostalo dana godišnjeg: {{ available }}';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
