<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayPalController;
use Srmklive\PayPal\Services\PayPal;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
 

    Route::post('/wallet/paypal', [PayPalController::class, 'paypal'])->name('wallet.paypal');
    Route::get('/wallet/paypal/success', [PayPalController::class, 'success'])->name('wallet.paypal.success');
    Route::get('/wallet/paypal/cancel', [PayPalController::class, 'cancelPayment'])->name('wallet.paypal.cancel');
   
    Route::get('/wallet/balance', [WalletController::class, 'showBalance'])->name('wallet.balance');
    Route::post('/wallet/deduct', [WalletController::class, 'deduct'])->name('wallet.deduct');
});

require __DIR__.'/auth.php';
