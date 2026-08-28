<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\VolunteerController;
use App\Http\Controllers\CauseController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GetInvolvedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpactController;
use App\Http\Controllers\InitiativeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;

Route::get('/up', fn () => response()->json(['status' => 'ok']))->name('up');

// Dashboard alias – redirects to the admin panel
Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
});

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/search', SearchController::class)->name('search');

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

// Get Involved
Route::get('/get-involved', [GetInvolvedController::class, 'index'])->name('get-involved');
Route::post('/get-involved', [GetInvolvedController::class, 'store'])->name('get-involved.store');

// Coming Soon pages
Route::get('/coming-soon-get-involved', fn () => redirect()->route('get-involved'));

// API endpoints (JSON/AJAX)
Route::post('/api/newsletter', [NewsletterController::class, 'store'])->name('api.newsletter');
Route::post('/api/volunteer-interest', [VolunteerController::class, 'store'])->name('api.volunteer');

Route::middleware('auth')->group(function () {
    Route::post('/api/upload', [UploadController::class, 'store'])->name('api.upload');
    Route::post('/api/upload/image', [UploadController::class, 'image'])->name('api.upload.image');
    Route::post('/api/upload/document', [UploadController::class, 'document'])->name('api.upload.document');
    Route::post('/api/upload/media', [UploadController::class, 'media'])->name('api.upload.media');
});

// Admin routes
require __DIR__.'/admin.php';

// Auth routes (Breeze)
require __DIR__.'/auth.php';
