<?php

namespace UserSessionBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserSessionInvalidatedEvent extends Event
{
    public function __construct(
        public readonly string $sessionId
    ) {}
}
