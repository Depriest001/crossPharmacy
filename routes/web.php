<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;

use App\Http\Controllers\AuthController\AuthController;
use App\Http\Controllers\AdminController\AdminController;
use App\Http\Controllers\AdminController\AdminStaffController;
use App\Http\Controllers\AdminController\BranchController;
use App\Http\Controllers\AdminController\RoleController;
use App\Http\Controllers\AdminController\ProductController;
use App\Http\Controllers\AdminController\ProductCategoryController;
use App\Http\Controllers\AdminController\StockController;
use App\Http\Controllers\AdminController\SaleController;

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot.password');
});

// Protected admin routes
Route::prefix('admin')->middleware('auth:staff')->group(function () {

    Route::get('/export/{table}/{type}', [ExportController::class, 'export'])->name('export.data');

    Route::put('/staff/profile/update', [AdminController::class, 'updateProfile'])
        ->name('staff.profile.update');

    Route::put('/staff/password/update', [AdminController::class, 'updatePassword'])
        ->name('staff.password.update');
        
    Route::get('/', [AdminController::class, 'admindashboard'])->name('admin.dashboard');
    Route::get('/profile', [AdminController::class, 'adminprofile'])->name('admin.profile');
    
    Route::get('/stock', [StockController::class, 'index'])->name('admin.stock.index');
    Route::post('/stock/adjust', [StockController::class, 'storeAdjustment'])->name('stock.adjust.store');

    Route::resource('staffs', AdminStaffController::class);
    Route::resource('branch', BranchController::class);
    Route::resource('role', RoleController::class);
    Route::resource('product', ProductController::class);
    Route::resource('category', ProductCategoryController::class);
    Route::resource('sale', SaleController::class);

    Route::get('/product/barcode/{barcode}', [SaleController::class, 'getProductByBarcode']);
    Route::get('/sale/{sale}/receipt', [SaleController::class, 'printReceipt'])
    ->name('admin.sale.receipt');
});