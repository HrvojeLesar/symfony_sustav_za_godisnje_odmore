<?php

namespace App\Validator;

use App\Entity\AnnualVacation;
use App\Entity\User;
use App\Entity\VacationRequest;
use App\Repository\AnnualVacationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use UnexpectedValueException;

class VacationRequestValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager, private Security $security)
    {
    }

    /**
     * @param VacationRequest $value
     * @param App\Validator\VacationRequest $constraint
     */
    public function validate($vacationRequest, Constraint $constraint): void
    {
        if (!$vacationRequest instanceof VacationRequest) {
            throw new UnexpectedValueException($vacationRequest, VacationRequest::class);
        }

        $from = $vacationRequest->getFromDate();
        $to = $vacationRequest->getToDate();

        $requestedVacationDays = intval($to->diff($from)->format('%a'));
        $availableVacationDays = $this->availableVacationDays();

        if ($from > $to) {
            $this->context->buildViolation($constraint->datesOutOfSync)->addViolation();
        }
        if ($requestedVacationDays === 0) {
            $this->context->buildViolation($constraint->zeroDaysRequested)->addViolation();
        }
        if ($requestedVacationDays > $availableVacationDays) {
            $this->context
                ->buildViolation($constraint->notEnoughAvailableVacationDays)
                ->setParameter('{{ available }}', $availableVacationDays)
                ->setParameter('{{ requested }}', $requestedVacationDays)
                ->addViolation()
            ;
        }
    }

    private function availableVacationDays(): int
    {
        /** @var AnnualVacationRepository $annualVacationRepo */
        $annualVacationRepo = $this->entityManager->getRepository(AnnualVacation::class);
        /** @var User $user */
        $user = $this->security->getUser();

        /** @var AnnualVacation|null $annualVacation  =*/
        $annualVacation = $user->getAnnualVacations()
            ->filter(function (AnnualVacation $av) {
                return $av->getYear() === date('Y');
            })
            ->first();

        if (is_null($annualVacation)) {
            throw new Exception("Annual vacation not found");
        }
        return $annualVacation->availableVacationDays();
    }
}
