<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PartnerEnquiryController;
use App\Http\Controllers\DotaPubRecordController;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('dota-pub-tracker', function () {
    return Inertia::render('DotaPubTracker');
})->middleware(['auth', 'verified'])->name('dota-pub-tracker');

Route::get('taryahan-cs', function () {
    return Inertia::render('TaryahanCs');
})->middleware(['auth', 'verified'])->name('taryahan-cs');

Route::get('partner-enquiries', function () {
    return Inertia::render('PartnerEnquiries');
})->middleware(['auth', 'verified'])->name('partner-enquiries');

Route::get('members', function () {
    return Inertia::render('Members');
})->middleware(['auth', 'verified'])->name('members');

Route::get('merch-store', function () {
    return Inertia::render('MerchStore');
})->middleware(['auth', 'verified'])->name('merch-store');

Route::get('admin-users', function () {
    return Inertia::render('AdminUsers');
})->middleware(['auth', 'verified'])->name('admin-users');  

// Public endpoint for submitting partner enquiries
Route::post('/partner-enquiries', [PartnerEnquiryController::class, 'store']);

// Protected API endpoints for managing partner enquiries
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/api/partner-enquiries', [PartnerEnquiryController::class, 'index']);
    Route::delete('/api/partner-enquiries/{id}', [PartnerEnquiryController::class, 'destroy']);
    Route::get('/api/partner-enquiries/statistics', [PartnerEnquiryController::class, 'statistics']);
});

// Add these for Dota Pub Tracker API (CORRECTED VERSION)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/api/dota-pub-records', [DotaPubRecordController::class, 'index']);
    Route::post('/api/dota-pub-records', [DotaPubRecordController::class, 'store']);
    Route::get('/api/dota-pub-records/statistics', [DotaPubRecordController::class, 'statistics']);
    Route::get('/api/dota-pub-records/{id}', [DotaPubRecordController::class, 'show']);
    Route::put('/api/dota-pub-records/{id}', [DotaPubRecordController::class, 'update']);
    Route::delete('/api/dota-pub-records/{id}', [DotaPubRecordController::class, 'destroy']);
});

require __DIR__.'/settings.php';