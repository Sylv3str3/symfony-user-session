<?php

namespace UserSessionBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserSessionDeletedEvent extends Event
{
    public function __construct(
        public readonly string $sessionId
    ) {}
}
