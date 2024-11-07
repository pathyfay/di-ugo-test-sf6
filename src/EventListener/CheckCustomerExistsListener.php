<?php

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckCustomerExistsListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => ['checkCustomerExists', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function checkCustomerExists(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        if (!$user instanceof User || !$event->getRequest()->isMethodSafe(false)) {
            return;
        }

        if ($user->attributes->get('_route') === '_api_/user/{id}_get') {
            throw new NotFoundHttpException(sprintf('The product "%s" does not exist.', $user->getId()));
        }
    }
}