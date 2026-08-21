<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CauseController;
use App\Http\Controllers\Admin\InitiativeController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ImpactController;
use App\Http\Controllers\Admin\StoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Causes
    Route::resource('causes', CauseController::class);

    // Initiatives
    Route::resource('initiatives', InitiativeController::class);

    // Events
    Route::resource('events', EventController::class);

    // Impact Activities
    Route::resource('impact', ImpactController::class);

    // Stories
    Route::resource('stories', StoryController::class);
});
