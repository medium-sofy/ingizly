<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\EmailVerificationController;
Route::get('/', function () {
    return view('welcome');
});

// Email Verification Routes
Route::get('/verify-email', [EmailVerificationController::class, 'showEmailForm'])->name('verification.email.form');
Route::post('/verify-email', [EmailVerificationController::class, 'sendVerificationCode'])->name('verification.send');
Route::get('/verify-code', [EmailVerificationController::class, 'showCodeForm'])->name('verification.code.form');
Route::post('/verify-code', [EmailVerificationController::class, 'verifyCode'])->name('verification.verify');