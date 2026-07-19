<?php

use App\Http\Controllers\VendorPortalController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/login', [VendorPortalController::class, 'loginForm'])->name('login');
    Route::post('/login', [VendorPortalController::class, 'login'])->name('login.post');
    Route::get('/dashboard', [VendorPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/history/{history}', [VendorPortalController::class, 'show'])->name('history.show');
    Route::post('/history/{history}/report', [VendorPortalController::class, 'updateReport'])->name('history.report');
    Route::post('/logout', [VendorPortalController::class, 'logout'])->name('logout');
});
