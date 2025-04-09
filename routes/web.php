<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/paymob/order', [PaymentController::class, 'createOrder']);
Route::post('/paymob/payment-key', [PaymentController::class, 'generatePaymentKey']);
