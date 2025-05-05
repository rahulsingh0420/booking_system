<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\BookingController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::post('/approve-renter/{user}', [DashboardController::class, 'approveRenter'])
            ->name('approve-renter');
        Route::post('/renters/{user}/approve', [DashboardController::class, 'approveRenter'])
            ->name('renters.approve');
        Route::post('/renters/{user}/reject', [DashboardController::class, 'rejectRenter'])
            ->name('renters.reject');
        Route::post('/renters/{user}/deactivate', [DashboardController::class, 'deactivateRenter'])
            ->name('renters.deactivate');
            
        Route::resource('listings', ListingController::class)->names([
            'index' => 'listings.index',
            'create' => 'listings.create',
            'store' => 'listings.store',
            'show' => 'listings.show',
            'edit' => 'listings.edit',
            'update' => 'listings.update',
            'destroy' => 'listings.destroy',
        ]);
        
        Route::resource('bookings', BookingController::class)->names([
            'index' => 'bookings.index',
            'create' => 'bookings.create',
            'store' => 'bookings.store',
            'show' => 'bookings.show',
            'edit' => 'bookings.edit',
            'update' => 'bookings.update',
            'destroy' => 'bookings.destroy',
        ]);
    });

    // Renter routes
    Route::prefix('renter')->middleware('role:renter', 'approved')->name('renter.')->group(function () {
        Route::resource('listings', ListingController::class)->names([
            'index' => 'listings.index',
            'create' => 'listings.create',
            'store' => 'listings.store',
            'show' => 'listings.show',
            'edit' => 'listings.edit',
            'update' => 'listings.update',
            'destroy' => 'listings.destroy',
        ]);
        
        Route::resource('bookings', BookingController::class)->names([
            'index' => 'bookings.index',
            'create' => 'bookings.create',
            'store' => 'bookings.store',
            'show' => 'bookings.show',
            'edit' => 'bookings.edit',
            'update' => 'bookings.update',
            'destroy' => 'bookings.destroy',
        ]);
    });

    // Tenant routes
    Route::prefix('tenant')->middleware('role:tenant')->name('tenant.')->group(function () {
        Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
        Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        
        Route::resource('bookings', BookingController::class)->except(['create'])->names([
            'index' => 'bookings.index',
            'store' => 'bookings.store',
            'show' => 'bookings.show',
            'edit' => 'bookings.edit',
            'update' => 'bookings.update',
            'destroy' => 'bookings.destroy',
        ]);
    });

    // Common routes for all authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/register/pending', function () {
    return view('auth.pending-approval');
})->name('register.pending');

Route::get('/not-approved', function () {
    return view('auth.not-approved');
})->name('not-approved')->middleware('auth');

require __DIR__.'/auth.php';
