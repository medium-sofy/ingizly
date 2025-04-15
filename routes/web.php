<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceDetailsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\NotificationController;



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



     // Booking routes (temporary - remove buyer_id when auth is implemented)
Route::post('/services/{service}/book', [ServiceBookingController::class, 'bookService'])
->name('service.book');

Route::post('/orders/{order}/confirm', [ServiceBookingController::class, 'confirmOrder'])
->name('orders.confirm');

// Notification routes
Route::get('/notifications', [NotificationController::class, 'index'])
->name('notifications.index');

Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])
->name('notifications.mark-read');