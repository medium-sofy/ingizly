<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\UserController;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/paymob/order', [PaymentController::class, 'createOrder']);
Route::post('/paymob/payment-key', [PaymentController::class, 'generatePaymentKey']);

//Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {});
    // admin routes
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/create', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{users}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{users}/edit', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{users}/destroy', [UserController::class, 'destroy'])->name('admin.users.destroy');
    // admin services list route
    Route::get('/admin/services', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::get('admin/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/admin/services/create', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/admin/services/{service}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/admin/services/{service}/edit', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');
    Route::get('/{service}', [ServiceController::class, 'show'])->name('admin.services.show');
    Route::post('/services/{id}/approve', [AdminController::class, 'approveService'])->name('services.approve');
    Route::post('/services/{id}/reject', [AdminController::class, 'rejectService'])->name('services.reject');
    //// Show single service details
    //Route::get('/{users}', [ServiceController::class, 'show'])->name('admin.users.show');



