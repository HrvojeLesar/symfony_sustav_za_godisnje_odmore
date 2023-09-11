<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RequestRateLimitSubscriber implements EventSubscriberInterface
{
    public function __construct(protected RateLimiterFactory $authenticatedApiLimiter)
    {
    }

    /**
     * @return void
     * @throws TooManyRequestsHttpException
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $route = $request->get('_route');
        if (str_contains($route, '_api')) {
            $limiter = $this->authenticatedApiLimiter->create($event->getRequest()->getClientIp());
            if ($limiter->consume()->isAccepted() === false) {
                throw new TooManyRequestsHttpException();
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
