<?php

namespace App\MessageHandler;

use App\Entity\VacationRequestStatus;
use App\Message\ApprovalNotification;
use App\Repository\VacationRequestRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler]
class ApprovalNotificationHandler
{
    public function __construct(
        protected VacationRequestRepository $vacationRequestRepository,
        protected MailerInterface $mailer,
        protected TranslatorInterface $translator,
    ) {
    }

    public function __invoke(ApprovalNotification $approvalNotification): void
    {
        $vacationRequest = $this->vacationRequestRepository->find($approvalNotification->getVacationRequestId());

        if (is_null($vacationRequest) || $vacationRequest->getStatus() !== VacationRequestStatus::Approved) {
            return;
        }

        $email = (new Email())
        ->from('system@go.com')
        ->to($vacationRequest->getUser()->getEmail())
        ->subject($this->translator->trans('email.approved.subject'))
        ->text($this->translator->trans('email.approved.text', [
            'days' => $vacationRequest->getDaysRequested(),
            'from' => $vacationRequest->getFromDate()->format('d.m.Y'),
            'to' => $vacationRequest->getToDate()->format('d.m.Y'),
        ]))
        ;

        $this->mailer->send($email);
    }
}
