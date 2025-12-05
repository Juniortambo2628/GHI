<?php

/**
 * User Logged In Event
 * Global Harmony Initiative Website
 */

namespace GHI\Events;

use Symfony\Contracts\EventDispatcher\Event;

class UserLoggedInEvent extends Event
{
    public const NAME = 'user.logged_in';

    public function __construct(private readonly int $userId, private readonly string $email)
    {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
