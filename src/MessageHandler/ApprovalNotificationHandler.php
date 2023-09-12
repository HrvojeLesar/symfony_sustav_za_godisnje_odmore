<?php

namespace App\MessageHandler;

use App\Entity\VacationRequestStatus;
use App\Message\ApprovalNotification;
use App\Repository\VacationRequestRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class ApprovalNotificationHandler
{
    public function __construct(
        protected VacationRequestRepository $vacationRequestRepository,
        protected MailerInterface $mailer
    ) {
    }

    public function __invoke(ApprovalNotification $approvalNotification): void
    {
        $vacationRequest = $this->vacationRequestRepository->find($approvalNotification->getVacationRequestId());
        if (! is_null($vacationRequest) && $vacationRequest->getStatus() === VacationRequestStatus::Approved) {
            $email = (new Email())
                ->from('system@go.com')
                ->to($vacationRequest->getUser()->getEmail())
                ->subject('Odobrenje zahtjeva za godišnji odmor')
                ->text(sprintf(
                    'Zahtjev za godišnji odmor u razdoblju %s-%s je odobren.',
                    $vacationRequest->getFromDate()->format('d.m.Y'),
                    $vacationRequest->getToDate()->format('d.m.Y')
                ))
            ;

            $this->mailer->send($email);
        }
    }
}
