<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ServiceBuyerController;
use App\Http\Controllers\Auth\ServiceProviderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceDetailsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register',[RegisteredUserController::class]);
Route::post('/register',[RegisteredUserController::class]);

Route::middleware('auth')->group(function () {
    Route::get('/choose-role', [RegisteredUserController::class, 'showRoleSelection'])->name('choose.role');
    Route::post('/select-role', [RegisteredUserController::class, 'selectRole'])->name('select.role');

    // Service Provider routes
    Route::get('/service-provider/form', [ServiceProviderController::class, 'create'])->name('service_provider.form');
    Route::post('/service-provider/store', [ServiceProviderController::class, 'store'])->name('service_provider.store');

    // Service Buyer routes
    Route::get('/service-buyer/form', [ServiceBuyerController::class, 'create'])->name('service_buyer.form');
    Route::post('/service-buyer/store', [ServiceBuyerController::class, 'store'])->name('service_buyer.store');
});

Route::get('/dashboard', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'service_buyer' => redirect()->route('buyer.dashboard'),
            'service_provider' => redirect()->route('provider.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect('/login'),
        };
    }
    return redirect('/login');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('services', \App\Http\Controllers\Provider\ServiceController::class);

use App\Http\Controllers\Provider\ServiceProviderDashboardController;

Route::get('/provider/dashboard', [ServiceProviderDashboardController::class, 'index'])->name('provider.dashboard');
require __DIR__.'/auth.php';

Route::post('/paymob/order', [PaymentController::class, 'createOrder']);
Route::post('/paymob/payment-key', [PaymentController::class, 'generatePaymentKey']);





// Protected routes for authenticated users
Route::middleware(['auth'])->group(function () {
    // Review routes
    Route::post('/services/{serviceId}/review', [ServicedetailsController::class, 'submitReview'])
         ->name('service.review.submit');
         
         
         Route::get('/services/{id}', [ServicedetailsController::class, 'show'])
         ->name('service.details');
    Route::post('/services/{service}/book', [ServiceBookingController::class, 'bookService'])
         ->name('service.book');
         
    Route::post('/orders/{order}/confirm', [ServiceBookingController::class, 'confirmOrder'])
         ->name('orders.confirm');

    // Report routes
    Route::get('/services/{serviceId}/report', [ServicedetailsController::class, 'showReportForm'])
         ->name('service.report.form');
         
    Route::post('/services/{serviceId}/report', [ServicedetailsController::class, 'submitReport'])
         ->name('service.report.submit');
});
// Notification routes
Route::get('/notifications', [NotificationController::class, 'index'])
->name('notifications.index');

Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])
->name('notifications.mark-read');