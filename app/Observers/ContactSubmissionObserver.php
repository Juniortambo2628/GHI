<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\ContactSubmission;
use App\Models\User;

class ContactSubmissionObserver
{
    public function created(ContactSubmission $contact): void
    {
        $admin = User::where('is_admin', true)->first() ?? User::first();

        if ($admin) {
            AdminNotification::create([
                'user_id' => $admin->id,
                'type' => 'contact',
                'title' => 'New Contact',
                'message' => "{$contact->firstname} {$contact->lastname} sent a message",
            ]);
        }
    }
}
