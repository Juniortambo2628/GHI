<?php

/**
 * Email Sent Event
 * Global Harmony Initiative Website
 */

namespace GHI\Events;

use Symfony\Contracts\EventDispatcher\Event;

class EmailSentEvent extends Event
{
    public const NAME = 'email.sent';

    public function __construct(private readonly string $to, private readonly string $subject, private readonly bool $success)
    {
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
