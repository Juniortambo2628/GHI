<?php

/**
 * Event Service using Symfony Event Dispatcher
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Symfony\Component\EventDispatcher\EventDispatcher;

class EventService
{
    private static ?EventDispatcher $instance = null;

    /**
     * Get Event Dispatcher instance (Singleton)
     */
    public static function getInstance(): EventDispatcher
    {
        if (!self::$instance instanceof \Symfony\Component\EventDispatcher\EventDispatcher) {
            self::$instance = new EventDispatcher();
        }

        return self::$instance;
    }

    /**
     * Dispatch an event
     */
    public static function dispatch(object $event, string $eventName = null): object
    {
        try {
            $dispatcher = self::getInstance();

            if ($eventName === null) {
                $eventName = $event::class;
            }

            $dispatcher->dispatch($event, $eventName);

            if (function_exists('log_message')) {
                log_message('debug', 'Event dispatched', [
                    'event' => $eventName,
                ]);
            }

            return $event;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Event dispatch failed', [
                    'event' => $eventName ?? 'unknown',
                    'error' => $exception->getMessage(),
                ]);
            }

            throw $exception;
        }
    }

    /**
     * Add event listener
     */
    public static function listen(string $eventName, callable $listener, int $priority = 0): void
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = self::getInstance();
        $dispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * Remove event listener
     */
    public static function removeListener(string $eventName, callable $listener): void
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = self::getInstance();
        $dispatcher->removeListener($eventName, $listener);
    }

    /**
     * Get all listeners for an event
     */
    public static function getListeners(?string $eventName = null): array
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = self::getInstance();
        return $dispatcher->getListeners($eventName);
    }
}
