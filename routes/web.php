<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ZoomBookingController;
use App\Http\Controllers\ZoomAccountController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Middleware\EnsureAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Manajemen & Booking Zoom
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Zoom Bookings (User & Admin)
    Route::resource('bookings', ZoomBookingController::class);
    Route::post('bookings/{booking}/approve', [ZoomBookingController::class, 'approve'])->name('bookings.approve');
    Route::post('bookings/{booking}/reject', [ZoomBookingController::class, 'reject'])->name('bookings.reject');
    Route::post('bookings/{booking}/generate-meeting', [ZoomBookingController::class, 'generateMeeting'])->name('bookings.generate-meeting');

    // Calendar
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Memo & PDF Printing
    Route::resource('memos', MemoController::class);
    Route::get('memos/{memo}/pdf', [MemoController::class, 'printPdf'])->name('memos.pdf');

    // Admin Only Area
    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::get('zoom-accounts/sync-preview', [ZoomAccountController::class, 'syncPreview'])->name('zoom-accounts.sync-preview');
        Route::post('zoom-accounts/sync-execute', [ZoomAccountController::class, 'syncExecute'])->name('zoom-accounts.sync-execute');
        Route::resource('zoom-accounts', ZoomAccountController::class);
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });
});
