<?php

/**
 * Event Listeners Configuration
 * Global Harmony Initiative Website
 * 
 * Register event listeners here
 */

use GHI\Events\UserLoggedInEvent;
use GHI\Events\EmailSentEvent;
use GHI\Events\ContentCreatedEvent;

// Example: Listen to user login events
event_listen(UserLoggedInEvent::NAME, function (UserLoggedInEvent $event) {
    // Clear user-specific cache
    cache_delete('user_' . $event->getUserId());
    
    // Log user activity
    log_message('info', 'User logged in event handled', [
        'user_id' => $event->getUserId(),
        'email' => $event->getEmail(),
    ]);
}, 0);

// Example: Listen to email sent events
event_listen(EmailSentEvent::NAME, function (EmailSentEvent $event) {
    if ($event->isSuccess()) {
        // Track successful email sends
        log_message('debug', 'Email sent event handled', [
            'to' => $event->getTo(),
            'subject' => $event->getSubject(),
        ]);
    }
}, 0);

// Example: Listen to content created events
event_listen(ContentCreatedEvent::NAME, function (ContentCreatedEvent $event) {
    // Clear content cache when new content is created
    cache_clear();
    
    log_message('info', 'Content created event handled', [
        'type' => $event->getContentType(),
        'id' => $event->getContentId(),
    ]);
}, 0);

