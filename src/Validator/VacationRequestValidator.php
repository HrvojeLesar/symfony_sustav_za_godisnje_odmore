<?php

namespace App\Validator;

use App\Entity\AnnualVacation;
use App\Entity\User;
use App\Entity\VacationRequest;
use App\Repository\AnnualVacationRepository;
use App\Validator\VacationRequest as VacationRequestConstraint;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;
use UnexpectedValueException;

class VacationRequestValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager, private Security $security, private TranslatorInterface $translator)
    {
    }

    /**
     * @param VacationRequest $value
     * @param VacationRequestConstraint $constraint
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
            $this->context->buildViolation($constraint->getViolationString(VacationRequestValidationError::DatesOutOfSync, $this->translator))->addViolation();
        }
        if ($requestedVacationDays === 0) {
            $this->context->buildViolation($constraint->getViolationString(VacationRequestValidationError::ZeroDaysRequested, $this->translator))->addViolation();
        }
        if ($requestedVacationDays > $availableVacationDays) {
            $this->context
                ->buildViolation($constraint->getViolationString(VacationRequestValidationError::NotEnoughtAvailableVacationDays, $this->translator))
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

        return $user->getAvailableVacationDays();
    }
}
