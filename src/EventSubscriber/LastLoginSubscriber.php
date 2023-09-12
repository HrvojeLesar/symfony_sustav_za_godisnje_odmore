<?php

namespace App\EventSubscriber;

use App\EventDispatcher\LoginSuccessEvent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LastLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent'
        ];
    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $user->setLastLoginDate(new DateTime('now'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

}
