<?php

namespace App\Providers;

use App\Models\ContactSubmission;
use App\Models\NewsletterSubscriber;
use App\Observers\ContactSubmissionObserver;
use App\Observers\NewsletterSubscriberObserver;
use Illuminate\Support\ServiceProvider;

class AdminNotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        ContactSubmission::observe(ContactSubmissionObserver::class);
        NewsletterSubscriber::observe(NewsletterSubscriberObserver::class);
    }
}
