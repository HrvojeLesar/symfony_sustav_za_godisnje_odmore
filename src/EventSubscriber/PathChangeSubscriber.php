<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PathChangeSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $uri = $request->getRequestUri();

        if ($request->getMethod() !== 'GET' || !str_contains($uri, 'employee')) {
            return;
        }

        $last_visited = $request->cookies->get('last_visited');
        if (is_null($last_visited) || $last_visited !== $uri) {
            $cookie = new Cookie('last_visited', $uri);
            $event->getResponse()->headers->setCookie($cookie);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
