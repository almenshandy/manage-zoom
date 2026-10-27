<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ZoomBookingController;
use App\Http\Controllers\ZoomAccountController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingReportController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\ProjectController;
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
    Route::get('bookings/{booking}/print', [BookingReportController::class, 'printBooking'])->name('bookings.print');
    Route::get('bookings-report/print-all', [BookingReportController::class, 'printAll'])->name('bookings.print-all');

    // Calendar
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Memo & PDF Printing
    Route::resource('memos', MemoController::class);
    Route::get('memos/{memo}/pdf', [MemoController::class, 'printPdf'])->name('memos.pdf');

    // Admin Only Area
    Route::middleware(EnsureAdmin::class)->group(function () {
        // Zoom Accounts Management & Sync
        Route::get('zoom-accounts/export-pdf', [ZoomAccountController::class, 'exportPdf'])->name('zoom-accounts.export-pdf');
        Route::get('zoom-accounts/sync-preview', [ZoomAccountController::class, 'syncPreview'])->name('zoom-accounts.sync-preview');
        Route::post('zoom-accounts/sync-execute', [ZoomAccountController::class, 'syncExecute'])->name('zoom-accounts.sync-execute');
        Route::post('zoom-accounts/{zoom_account}/test-connection', [ZoomAccountController::class, 'testConnection'])->name('zoom-accounts.test-connection');
        Route::resource('zoom-accounts', ZoomAccountController::class);

        // Zoom API Global Settings
        Route::get('settings/zoom', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings/zoom', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        // User Management & Remote Sync
        Route::get('users/sync-preview', [UserController::class, 'syncPreview'])->name('users.sync-preview');
        Route::post('users/sync-execute', [UserController::class, 'syncExecute'])->name('users.sync-execute');
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Master Proyek
        Route::resource('projects', ProjectController::class)->except(['create', 'show', 'edit']);

        // Database Backup & Export
        Route::get('backup', [DatabaseBackupController::class, 'index'])->name('backup.index');
        Route::get('backup/table/{table}', [DatabaseBackupController::class, 'downloadTable'])->name('backup.download-table');
        Route::get('backup/download-all', [DatabaseBackupController::class, 'downloadAll'])->name('backup.download-all');

        // Activity Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });
});
