<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ServiceController as AdminServiceController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ServiceBuyerController;
use App\Http\Controllers\Auth\ServiceProviderController;
use App\Http\Controllers\Buyer\CheckoutController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Buyer\ServiceBuyerDashboardController;

use App\Http\Controllers\Buyer\ServiceController as ServiceBuyerCatalogController;
use App\Http\Controllers\Provider\ServiceController as ServiceProviderCatalogController;

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceDetailsController;
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

//Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {});
    // admin routes
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/create', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{users}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{users}/edit', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{users}/destroy', [UserController::class, 'destroy'])->name('admin.users.destroy');
    // admin services list route
    Route::get('/admin/services', [AdminServiceController::class, 'index'])->name('admin.services.index');
    Route::get('admin/services/create', [AdminServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/admin/services/create', [AdminServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/admin/services/{service}/edit', [AdminServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/admin/services/{service}/edit', [AdminServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/admin/services/show/{service}', [AdminServiceController::class, 'destroy'])->name('admin.services.destroy');
    Route::get('/admin/services/show/{service}', [AdminServiceController::class, 'show'])->name('admin.services.show');
    Route::post('/admin/services/{id}/approve', [AdminController::class, 'approveService'])->name('services.approve');
    Route::post('/admin/services/{id}/reject', [AdminController::class, 'rejectService'])->name('services.reject');
    //// Show single service details
    //Route::get('/{users}', [ServiceController::class, 'show'])->name('admin.users.show');


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

Route::resource('services', ServiceProviderCatalogController::class);

use App\Http\Controllers\Provider\ServiceProviderDashboardController;
Route::get('/provider/dashboard', [ServiceProviderDashboardController::class, 'index'])->name('provider.dashboard');
require __DIR__.'/auth.php';
Route::post('/payment/process', [PaymentController::class, 'paymentProcess'])->name('payment.process');;
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);
Route::get('/payment-success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment-failed', [PaymentController::class, 'failed'])->name('payment.failed');
// Service Buyer Routes
Route::middleware(['auth', 'role:service_buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [ServiceBuyerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('orders', OrderController::class);

    // Service browsing routes

    Route::get('/services', [ServiceBuyerCatalogController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [ServiceBuyerCatalogController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/order', [ServiceBuyerCatalogController::class, 'order'])->name('services.order');

});

// Checkout Routes
Route::middleware(['auth', 'role:service_buyer'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/{order}', [CheckoutController::class, 'show'])->name('show');
    Route::post('/{order}/process', [CheckoutController::class, 'process'])->name('process');
});
//
//Route::post('/paymob/order', [PaymentController::class, 'createOrder']);
//Route::post('/paymob/payment-key', [PaymentController::class, 'generatePaymentKey']);

// Service Details Routes
// Route::get('/services/{id}', [ServicedetailsController::class, 'show'])
//      ->name('service.details');

Route::post('/services/{serviceId}/review', [ServicedetailsController::class, 'submitReview'])
     ->name('service.review.submit');

Route::get('/services/{serviceId}/report', [ServicedetailsController::class, 'showReportForm'])
     ->name('service.report.form');

Route::post('/services/{serviceId}/report', [ServicedetailsController::class, 'submitReport'])
     ->name('service.report.submit');

     // Booking routes (temporary - remove buyer_id when auth is implemented)
Route::post('/services/{service}/book', [ServiceBookingController::class, 'bookService'])
->name('service.book');

Route::get('/services/{id}', [ServiceDetailsController::class, 'show'])
     ->name('service.details');

Route::post('/orders/{order}/confirm', [ServiceBookingController::class, 'confirmOrder'])
->name('orders.confirm');

// Notification routes
Route::get('/notifications', [NotificationController::class, 'index'])
->name('notifications.index');

Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])
->name('notifications.mark-read');
