<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebinarController;

/*
|--------------------------------------------------------------------------
| Webinar Routes
|--------------------------------------------------------------------------
*/

Route::prefix('webinars')->name('webinars.')->group(function () {

    // Public routes
    Route::get('/', [WebinarController::class, 'index'])->name('index');
    Route::get('/calendar', [WebinarController::class, 'calendar'])->name('calendar');
    Route::get('/{webinar:slug}', [WebinarController::class, 'show'])->name('show');

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/{webinar:slug}/register', [WebinarController::class, 'register'])->name('register');
        Route::delete('/{webinar:slug}/unregister', [WebinarController::class, 'unregister'])->name('unregister');
        Route::get('/{webinar:slug}/join', [WebinarController::class, 'join'])->name('join');
        Route::get('/{webinar:slug}/join/{token}', [WebinarController::class, 'join'])->name('join.token');
        Route::post('/{webinar:slug}/feedback', [WebinarController::class, 'feedback'])->name('feedback');
        Route::get('/my/webinars', [WebinarController::class, 'myWebinars'])->name('my');
    });

    // Webhook routes (no CSRF protection)
    Route::post('/webhooks/{platform}', [WebinarController::class, 'platformWebhook'])
        ->name('webhook')
        ->withoutMiddleware(['web']);
});

/*
|--------------------------------------------------------------------------
| Admin Webinar Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin/webinars')->name('admin.webinars.')->middleware(['auth', 'role:admin'])->group(function () {

    // Webinar management
    Route::get('/', [App\Http\Controllers\Admin\WebinarController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\WebinarController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\WebinarController::class, 'store'])->name('store');
    Route::get('/{webinar}/edit', [App\Http\Controllers\Admin\WebinarController::class, 'edit'])->name('edit');
    Route::put('/{webinar}', [App\Http\Controllers\Admin\WebinarController::class, 'update'])->name('update');
    Route::delete('/{webinar}', [App\Http\Controllers\Admin\WebinarController::class, 'destroy'])->name('destroy');

    // Webinar actions
    Route::post('/{webinar}/publish', [App\Http\Controllers\Admin\WebinarController::class, 'publish'])->name('publish');
    Route::post('/{webinar}/unpublish', [App\Http\Controllers\Admin\WebinarController::class, 'unpublish'])->name('unpublish');
    Route::post('/{webinar}/feature', [App\Http\Controllers\Admin\WebinarController::class, 'feature'])->name('feature');
    Route::post('/{webinar}/unfeature', [App\Http\Controllers\Admin\WebinarController::class, 'unfeature'])->name('unfeature');

    // Registrations management
    Route::get('/{webinar}/registrations', [App\Http\Controllers\Admin\WebinarController::class, 'registrations'])->name('registrations');
    Route::get('/{webinar}/registrations/export', [App\Http\Controllers\Admin\WebinarController::class, 'exportRegistrations'])->name('registrations.export');

    // Platform integrations
    Route::get('/integrations', [App\Http\Controllers\Admin\PlatformIntegrationController::class, 'index'])->name('integrations.index');
    Route::get('/integrations/create', [App\Http\Controllers\Admin\PlatformIntegrationController::class, 'create'])->name('integrations.create');
    Route::post('/integrations', [App\Http\Controllers\Admin\PlatformIntegrationController::class, 'store'])->name('integrations.store');
    Route::get('/integrations/{integration}/edit', [App\Http\Controllers\Admin\PlatformIntegrationController::class, 'edit'])->name('integrations.edit');
    Route::put('/integrations/{integration}', [App\Http\Controllers\Admin\PlatformIntegrationController::class, 'update'])->name('integrations.update');
    Route::delete('/integrations/{integration}', [App\Http\Controllers\Admin\PlatformIntegrationController::class, 'destroy'])->name('integrations.destroy');
    Route::post('/integrations/{integration}/test', [App\Http\Controllers\Admin\PlatformIntegrationController::class, 'test'])->name('integrations.test');
});

/*
|--------------------------------------------------------------------------
| Instructor Webinar Routes
|--------------------------------------------------------------------------
*/

Route::prefix('instructor/webinars')->name('instructor.webinars.')->middleware(['auth', 'role:instructor'])->group(function () {

    // Webinar management
    Route::get('/', [App\Http\Controllers\Instructor\WebinarController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Instructor\WebinarController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Instructor\WebinarController::class, 'store'])->name('store');
    Route::get('/{webinar}/edit', [App\Http\Controllers\Instructor\WebinarController::class, 'edit'])->name('edit');
    Route::put('/{webinar}', [App\Http\Controllers\Instructor\WebinarController::class, 'update'])->name('update');
    Route::delete('/{webinar}', [App\Http\Controllers\Instructor\WebinarController::class, 'destroy'])->name('destroy');

    // Webinar actions
    Route::post('/{webinar}/publish', [App\Http\Controllers\Instructor\WebinarController::class, 'publish'])->name('publish');
    Route::post('/{webinar}/unpublish', [App\Http\Controllers\Instructor\WebinarController::class, 'unpublish'])->name('unpublish');
    Route::post('/{webinar}/start', [App\Http\Controllers\Instructor\WebinarController::class, 'start'])->name('start');
    Route::post('/{webinar}/end', [App\Http\Controllers\Instructor\WebinarController::class, 'end'])->name('end');

    // Registrations and attendance
    Route::get('/{webinar}/registrations', [App\Http\Controllers\Instructor\WebinarController::class, 'registrations'])->name('registrations');
    Route::get('/{webinar}/attendance', [App\Http\Controllers\Instructor\WebinarController::class, 'attendance'])->name('attendance');
    Route::post('/{webinar}/attendance/update', [App\Http\Controllers\Instructor\WebinarController::class, 'updateAttendance'])->name('attendance.update');

    // Analytics
    Route::get('/{webinar}/analytics', [App\Http\Controllers\Instructor\WebinarController::class, 'analytics'])->name('analytics');
    Route::get('/{webinar}/feedback', [App\Http\Controllers\Instructor\WebinarController::class, 'feedback'])->name('feedback');
});

/*
|--------------------------------------------------------------------------
| Organization Webinar Routes
|--------------------------------------------------------------------------
*/

Route::prefix('organization/webinars')->name('organization.webinars.')->middleware(['auth', 'role:organization'])->group(function () {

    // Webinar management
    Route::get('/', [App\Http\Controllers\Organization\WebinarController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Organization\WebinarController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Organization\WebinarController::class, 'store'])->name('store');
    Route::get('/{webinar}/edit', [App\Http\Controllers\Organization\WebinarController::class, 'edit'])->name('edit');
    Route::put('/{webinar}', [App\Http\Controllers\Organization\WebinarController::class, 'update'])->name('update');
    Route::delete('/{webinar}', [App\Http\Controllers\Organization\WebinarController::class, 'destroy'])->name('destroy');

    // Webinar actions
    Route::post('/{webinar}/publish', [App\Http\Controllers\Organization\WebinarController::class, 'publish'])->name('publish');
    Route::post('/{webinar}/unpublish', [App\Http\Controllers\Organization\WebinarController::class, 'unpublish'])->name('unpublish');

    // Registrations and analytics
    Route::get('/{webinar}/registrations', [App\Http\Controllers\Organization\WebinarController::class, 'registrations'])->name('registrations');
    Route::get('/{webinar}/analytics', [App\Http\Controllers\Organization\WebinarController::class, 'analytics'])->name('analytics');
    Route::get('/{webinar}/feedback', [App\Http\Controllers\Organization\WebinarController::class, 'feedback'])->name('feedback');
});





