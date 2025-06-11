<?php

namespace UserSessionBundle;

/**
 * User Session Bundle Events.
 */
final class Events
{
    /**
     * Dispatched when a new user session is created.
     *
     * @Event("UserSessionBundle\Event\UserSessionCreatedEvent")
     */
    public const USER_SESSION_CREATED = 'user_session.created';

    /**
     * Dispatched when a user session is deleted.
     *
     * @Event("UserSessionBundle\Event\UserSessionDeletedEvent")
     */
    public const USER_SESSION_DELETED = 'user_session.deleted';

    /**
     * Dispatched when a user session is invalidated.
     *
     * @Event("UserSessionBundle\Event\UserSessionInvalidatedEvent")
     */
    public const USER_SESSION_INVALIDATED = 'user_session.invalidated';
}