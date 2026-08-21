<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CauseController;
use App\Http\Controllers\InitiativeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ImpactController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\VolunteerController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Content pages
Route::get('/causes', [CauseController::class, 'index'])->name('causes.index');
Route::get('/causes/{cause}', [CauseController::class, 'show'])->name('causes.show');

Route::get('/initiatives', [InitiativeController::class, 'index'])->name('initiatives.index');
Route::get('/initiatives/{initiative}', [InitiativeController::class, 'show'])->name('initiatives.show');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/impact', [ImpactController::class, 'index'])->name('impact.index');
Route::get('/impact/{impact_activity}', [ImpactController::class, 'show'])->name('impact.show');

Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
Route::get('/stories/{story}', [StoryController::class, 'show'])->name('stories.show');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/donate', [DonateController::class, 'index'])->name('donate');
Route::post('/donate', [DonateController::class, 'store'])->name('donate.store');
Route::get('/donate/success', [DonateController::class, 'success'])->name('donate.success');

// Coming Soon pages
Route::get('/coming-soon-get-involved', fn () => inertia('ComingSoonGetInvolved'))->name('coming-soon.get-involved');
Route::get('/coming-soon-donate', fn () => inertia('Donate'))->name('coming-soon.donate');

// API endpoints (JSON/AJAX)
Route::post('/api/newsletter', [NewsletterController::class, 'store'])->name('api.newsletter');
Route::post('/api/volunteer-interest', [VolunteerController::class, 'store'])->name('api.volunteer');
Route::post('/api/upload', [UploadController::class, 'store'])->name('api.upload');
Route::post('/api/upload/image', [UploadController::class, 'image'])->name('api.upload.image');
Route::post('/api/upload/document', [UploadController::class, 'document'])->name('api.upload.document');

// Admin routes
require __DIR__.'/admin.php';

// Auth routes (Breeze)
require __DIR__.'/auth.php';
