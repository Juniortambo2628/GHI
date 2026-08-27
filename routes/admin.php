<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CauseController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FormDraftController;
use App\Http\Controllers\Admin\GetInvolvedSubmissionController;
use App\Http\Controllers\Admin\ImpactController;
use App\Http\Controllers\Admin\InitiativeController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\SystemStatusController;
use App\Http\Controllers\Admin\UserSecurityController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Causes
    Route::resource('causes', CauseController::class)->parameters(['causes' => 'cause:id']);

    // Initiatives
    Route::resource('initiatives', InitiativeController::class)->parameters(['initiatives' => 'initiative:id']);

    // Events
    Route::resource('events', EventController::class)->parameters(['events' => 'event:id']);
    Route::post('events/{event:id}/images', [EventController::class, 'syncImages'])->name('events.images');

    // Impact Activities
    Route::resource('impact', ImpactController::class)->parameters(['impact' => 'impact:id']);

    // Stories
    Route::resource('stories', StoryController::class)->parameters(['stories' => 'story:id']);

    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
    // Get Involved Submissions
    Route::get('get-involved', [GetInvolvedSubmissionController::class, 'index'])->name('get-involved.index');
    Route::get('get-involved/{id}', [GetInvolvedSubmissionController::class, 'show'])->name('get-involved.show');
    Route::put('get-involved/{id}', [GetInvolvedSubmissionController::class, 'update'])->name('get-involved.update');
    Route::delete('get-involved/{id}', [GetInvolvedSubmissionController::class, 'destroy'])->name('get-involved.destroy');

    Route::get('contacts', [ContactSubmissionController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contactSubmission}', [ContactSubmissionController::class, 'show'])->name('contacts.show');
    Route::put('contacts/{contactSubmission}', [ContactSubmissionController::class, 'update'])->name('contacts.update');
    Route::delete('contacts/{contactSubmission}', [ContactSubmissionController::class, 'destroy'])->name('contacts.destroy');
    Route::get('subscribers', [NewsletterSubscriberController::class, 'index'])->name('subscribers.index');
    Route::put('subscribers/{subscriber}', [NewsletterSubscriberController::class, 'update'])->name('subscribers.update');
    Route::delete('subscribers/{subscriber}', [NewsletterSubscriberController::class, 'destroy'])->name('subscribers.destroy');
    Route::get('media', [MediaLibraryController::class, 'index'])->name('media.index');
    Route::get('media/download/{path}', [MediaLibraryController::class, 'download'])->where('path', '.*')->name('media.download');
    Route::delete('media', [MediaLibraryController::class, 'destroy'])->name('media.destroy');
    Route::delete('media/bulk', [MediaLibraryController::class, 'bulkDestroy'])->name('media.bulk-destroy');
    Route::put('media/rename', [MediaLibraryController::class, 'rename'])->name('media.rename');
    Route::get('donations', fn () => redirect()->route('admin.dashboard'))->name('donations.index');
    Route::get('donations/{donation}', fn () => redirect()->route('admin.dashboard'))->name('donations.show');
    Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('exports/contacts', [ExportController::class, 'contacts'])->name('exports.contacts');
    Route::get('exports/subscribers', [ExportController::class, 'subscribers'])->name('exports.subscribers');
    Route::get('exports/donations', fn () => redirect()->route('admin.dashboard'))->name('exports.donations');
    Route::get('exports/{resource}', [ExportController::class, 'content'])->whereIn('resource', ['causes', 'initiatives', 'events', 'impact', 'stories'])->name('exports.content');

    // Search
    Route::get('search', [SearchController::class, 'search'])->name('search');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::put('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    // Form Drafts
    Route::post('drafts', [FormDraftController::class, 'save'])->name('drafts.save');
    Route::get('drafts', [FormDraftController::class, 'load'])->name('drafts.load');
    Route::delete('drafts', [FormDraftController::class, 'destroy'])->name('drafts.destroy');

    // Analytics Reports
    Route::get('analytics/report', [AnalyticsController::class, 'report'])->name('analytics.report');

    // System Status
    Route::get('system-status', [SystemStatusController::class, 'index'])->name('system-status');

    // Security Settings
    Route::get('security', [UserSecurityController::class, 'index'])->name('security');
    Route::get('security/passkeys/options', [UserSecurityController::class, 'passkeyOptions'])->name('security.passkeys.options');
    Route::post('security/passkeys', [UserSecurityController::class, 'passkeyRegister'])->name('security.passkeys.register');
    Route::delete('security/passkeys/{id}', [UserSecurityController::class, 'passkeyDelete'])->name('security.passkeys.delete');
    Route::put('security/password', [UserSecurityController::class, 'changePassword'])->name('security.password');
});
