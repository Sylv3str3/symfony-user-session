<?php

namespace UserSessionBundle\Subscriber;

use UserSessionBundle\Event\UserSessionCreatedEvent;
use UserSessionBundle\Event\UserSessionDeletedEvent;
use UserSessionBundle\Event\UserSessionInvalidatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use UserSessionBundle\Events;

class UserSessionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::USER_SESSION_CREATED => 'onSessionCreated',
            Events::USER_SESSION_DELETED => 'onSessionDeleted',
            Events::USER_SESSION_INVALIDATED => 'onSessionInvalidated',
        ];
    }

    public function onSessionCreated(UserSessionCreatedEvent $event): void
    {
        // Log or handle session creation
    }

    public function onSessionDeleted(UserSessionDeletedEvent $event): void
    {
        // Log or handle session deletion
    }

    public function onSessionInvalidated(UserSessionInvalidatedEvent $event): void
    {
        // Log or handle session invalidation
    }
}
