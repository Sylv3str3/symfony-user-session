<?php

namespace UserSessionBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use UserSessionBundle\Entity\UserSession;

class UserSessionCreatedEvent extends Event
{
    public function __construct(
        public readonly UserSession $session
    ) {}
}
