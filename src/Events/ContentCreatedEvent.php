<?php

/**
 * Content Created Event
 * Global Harmony Initiative Website
 */

namespace GHI\Events;

use Symfony\Contracts\EventDispatcher\Event;

class ContentCreatedEvent extends Event
{
    public const NAME = 'content.created';

    public function __construct(private readonly string $contentType, private readonly int $contentId, private readonly array $data = [])
    {
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
