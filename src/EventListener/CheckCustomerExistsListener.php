<?php

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Customer;
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
        $customer = $event->getControllerResult();
        if (!$customer instanceof Customer || !$event->getRequest()->isMethodSafe(false)) {
            return;
        }

        if ($customer->attributes->get('_route') === '_api_/customers/{id}_get') {
            throw new NotFoundHttpException(sprintf('The product "%s" does not exist.', $customer->getId()));
        }
    }
}