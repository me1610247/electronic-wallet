<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Define API routes that do not require session state or CSRF protection
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum')->name('logout');
    Route::get('/wallet/balance', [WalletController::class, 'showBalance'])->name('wallet.balance');
    Route::post('/wallet/deduct', [WalletController::class, 'deduct'])->name('wallet.deduct');
    Route::post('/wallet/paypal', [PayPalController::class, 'paypal'])->name('wallet.paypal');
    Route::get('/wallet/paypal/success', [PayPalController::class, 'success'])->name('wallet.paypal.success');
    Route::get('/wallet/paypal/cancel', [PayPalController::class, 'cancelPayment'])->name('wallet.paypal.cancel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
