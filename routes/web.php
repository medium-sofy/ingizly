<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\CustomReportController;
use App\Http\Controllers\admin\PaymentExportController;
use App\Http\Controllers\admin\ReviewController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\ServiceController as AdminServiceController;
use App\Http\Controllers\admin\UserController;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ServiceBuyerController;
use App\Http\Controllers\Auth\ServiceProviderController;

use App\Http\Controllers\Buyer\CheckoutController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Buyer\ServiceBuyerDashboardController;
use App\Http\Controllers\Buyer\ServiceController as ServiceBuyerCatalogController;
use App\Http\Controllers\Buyer\ServiceBuyerProfile;

use App\Http\Controllers\Provider\ServiceController as ServiceProviderCatalogController;
use App\Http\Controllers\Provider\ServiceProviderDashboardController;

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicCategoryController;
use App\Http\Controllers\ServiceDetailsController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\WelcomeController;

//@@ Home
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

//@@ Auth
require __DIR__.'/auth.php';
    // used to choose a specific role, and enter info specific to that role
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

//@@ Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users/create', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{users}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{users}/edit', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{users}/destroy', [UserController::class, 'destroy'])->name('users.destroy');

    // Admin services routes
    Route::get('services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::get('services/create', [AdminServiceController::class, 'create'])->name('services.create');
    Route::post('services/create', [AdminServiceController::class, 'store'])->name('services.store');
    Route::get('services/{service}/edit', [AdminServiceController::class,'edit'])->name('services.edit');
    Route::put('services/{service}/edit', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('services/show/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');
    Route::get('services/show/{service}', [AdminServiceController::class, 'show'])->name('services.show');
    Route::post('services/{id}/approve', [AdminController::class, 'approveService'])->name('services.approve');
    Route::post('services/{id}/reject', [AdminController::class, 'rejectService'])->name('services.reject');

    // Payment Export Routes
    Route::get('payments', [PaymentController::class, 'index'])->name('payments');
    Route::get('payments/export/pdf', [PaymentExportController::class, 'exportPDF'])->name('payments.export.pdf');
    Route::get('payments/export/csv', [PaymentExportController::class, 'exportCSV'])->name('payments.export.csv');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Reviews
    Route::resource('reviews', ReviewController::class)->only(['index', 'show', 'update', 'destroy']);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Violations (Reports) Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{violation}', [ReportController::class, 'show'])->name('reports.show');
    Route::put('/reports/{violation}', [ReportController::class, 'update'])->name('reports.update');

    // Custom Reports Routes
    Route::get('reports/custom', [CustomReportController::class, 'index'])->name('reports.custom.index');
    Route::post('reports/custom/generate', [CustomReportController::class, 'generate'])->name('reports.custom.generate');

    // Show single service details
    // Route::get('/{users}', [ServiceController::class, 'show'])->name('admin.users.show');
    Route::post('services/{id}/approve', [AdminController::class, 'approveService'])->name('services.approve');
    Route::post('services/{id}/reject', [AdminController::class, 'rejectService'])->name('services.reject');

    // Custom Reports Routes
    Route::get('reports/custom', [CustomReportController::class, 'index'])->name('reports.custom.index');
    Route::post('reports/custom/generate', [CustomReportController::class, 'generate'])->name('reports.custom.generate');
});

//@@ Service provider
Route::middleware(['auth', 'role:service_provider'])->prefix('provider')->group(function () {
    Route::get('profile', [ServiceProviderController::class, 'edit'])->name('service_provider.profile.edit');
    Route::put('profile', [ServiceProviderController::class, 'update'])->name('service_provider.profile.update');
    Route::put('profile/password', [ServiceProviderController::class, 'updatePassword'])->name('service_provider.profile.update_password');
    Route::delete('profile', [ServiceProviderController::class, 'deleteAccount'])->name('service_provider.profile.delete');
    // Provider dashboard services
    Route::resource('services', ServiceProviderCatalogController::class)->names('provider.services');
   // Route::delete('services/image/{image}', [ServiceProviderCatalogController::class, 'destroyImage'])->name('provider.services.image.destroy');
   Route::delete('services/image/{id}', [ServiceProviderCatalogController::class, 'destroyImage'])->name('provider.services.image.destroy');

    Route::get('dashboard', [ServiceProviderDashboardController::class, 'index'])->name('provider.dashboard');
    Route::post('dashboard/orders/{order}/accept', [ServiceProviderDashboardController::class, 'acceptOrder'])->name('provider.dashboard.accept');
    Route::post('dashboard/orders/{order}/reject', [ServiceProviderDashboardController::class, 'rejectOrder'])->name('provider.dashboard.reject');
    
    Route::get('/wallet', [ServiceProviderDashboardController::class, 'wallet'])->name('provider.wallet');
    Route::get('/wallet/download/{payment}', [ServiceProviderDashboardController::class, 'downloadTransaction'])->name('provider.wallet.download');
});

//@@ Service buyer
Route::middleware(['auth', 'role:service_buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [ServiceBuyerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('orders', OrderController::class);

    // Service buyer dashboard browsing routes
    Route::get('/services', [ServiceBuyerCatalogController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [ServiceBuyerCatalogController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/order', [ServiceBuyerCatalogController::class, 'order'])->name('services.order');
});
// Service buyer profile routes
Route::middleware(['auth', 'role:service_buyer'])->prefix('buyer')->group(function () {
    Route::get('/profile', [ServiceBuyerProfile::class, 'edit'])->name('service_buyer.profile.edit');
    Route::put('/profile', [ServiceBuyerProfile::class, 'update'])->name('service_buyer.profile.update');
    Route::put('/profile/password', [ServiceBuyerProfile::class, 'updatePassword'])->name('service_buyer.profile.update_password');
    Route::delete('/profile', [ServiceBuyerProfile::class, 'destroy'])->name('service_buyer.profile.delete');
});


//@@ Services (Home page view)
Route::middleware(['auth', 'role:service_buyer'])->group(function () {
    Route::post('/services/{serviceId}/review', [ServicedetailsController::class, 'submitReview'])
        ->name('service.review.submit');

    Route::get('/services/{serviceId}/report', [ServicedetailsController::class, 'showReportForm'])
        ->name('service.report.form');

    Route::post('/services/{serviceId}/report', [ServicedetailsController::class, 'submitReport'])
        ->name('service.report.submit');

    // Booking routes
    Route::post('/services/{service}/book', [ServiceBookingController::class, 'bookService'])
        ->name('service.book');

    // Route::get('/services/{id}', [ServiceDetailsController::class, 'show'])
    //     ->name('service.details');
});

//@@ Dashboard
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

//@@ Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//@@ Orders
Route::middleware(['auth', 'role:service_buyer'])->group(function () {
    Route::post('/orders/{order}/accept', [ServiceBookingController::class, 'acceptOrder'])
        ->name('orders.accept');

    Route::post('/orders/{order}/confirm', [ServiceBookingController::class, 'confirmOrder'])
        ->name('orders.confirm');

    Route::post('/orders/{order}/cancel', [ServiceBookingController::class, 'cancelOrder'])
        ->name('orders.cancel');

    Route::get('/order/payment/{order}', [ServiceBookingController::class, 'showPayment'])
        ->name('order.payment');
});

Route::middleware(['auth', 'role:service_buyer'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/{order}', [CheckoutController::class, 'show'])->name('show');
    Route::post('/{order}/process', [CheckoutController::class, 'process'])->name('process');
});

//@@ Payment
Route::post('/payment/process', [PaymentController::class, 'paymentProcess'])->name('payment.process');
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);
Route::get('/payment-success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment-failed', [PaymentController::class, 'failed'])->name('payment.failed');

//@@ Notifications
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

//@@ Categories
Route::get('All/categories', [PublicCategoryController::class, 'index'])->name('categories.index');
Route::get('categories/{category}', [PublicCategoryController::class, 'show'])->name('categories.show');
Route::get('/Allservices', [PublicCategoryController::class, 'allServices'])->name('services.all');
Route::get('/services/{id}', [ServiceDetailsController::class, 'show'])
        ->name('service.details');

