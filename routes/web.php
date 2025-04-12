<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceDetailsController;
use App\Http\Controllers\ReviewController;


Route::get('/', function () {
    return view('welcome');
})->name('home'); // 👈 This gives it the name 'home'

Route::post('/paymob/order', [PaymentController::class, 'createOrder']);
Route::post('/paymob/payment-key', [PaymentController::class, 'generatePaymentKey']);


// Service Details Routes
Route::get('/services/{id}', [ServicedetailsController::class, 'show'])
     ->name('service.details');

Route::post('/services/{serviceId}/review', [ServicedetailsController::class, 'submitReview'])
     ->name('service.review.submit');

Route::get('/services/{serviceId}/report', [ServicedetailsController::class, 'showReportForm'])
     ->name('service.report.form');

Route::post('/services/{serviceId}/report', [ServicedetailsController::class, 'submitReport'])
     ->name('service.report.submit');