<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerEnquiryController;

// Partner Enquiry API routes (protected by auth middleware)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/partner-enquiries', [PartnerEnquiryController::class, 'index']);
    Route::delete('/partner-enquiries/{id}', [PartnerEnquiryController::class, 'destroy']);
    Route::get('/partner-enquiries/statistics', [PartnerEnquiryController::class, 'statistics']);
});