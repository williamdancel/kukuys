<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

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

require __DIR__.'/settings.php';
