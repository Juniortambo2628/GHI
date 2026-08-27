<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\NewsletterSubscriber;
use App\Models\User;

class NewsletterSubscriberObserver
{
    public function created(NewsletterSubscriber $subscriber): void
    {
        $admin = User::where('is_admin', true)->first() ?? User::first();

        if ($admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'subscriber',
                'title' => 'New Subscriber',
                'message' => "{$subscriber->email} subscribed to newsletter",
            ]);
        }
    }
}
