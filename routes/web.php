<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use Midtrans\Config;

Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
Route::get('/booking/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/booking/calculate-price', [BookingController::class, 'calculatePrice'])->name('bookings.calculatePrice');
Route::post('/booking', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/booking/success', [BookingController::class, 'success'])->name('bookings.success');


Route::get('/midtrans-check', function () {
    Config::$serverKey = config('services.midtrans.server_key');
    Config::$isProduction = config('services.midtrans.is_production');
    Config::$isSanitized = config('services.midtrans.is_sanitized');
    Config::$is3ds = config('services.midtrans.is_3ds');

    return response()->json(['message' => 'Midtrans terkonfigurasi dengan benar']);
});