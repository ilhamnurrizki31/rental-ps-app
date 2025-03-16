<?php

use Illuminate\Http\Request;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MidtransIpWhitelist;

Route::middleware(MidtransIpWhitelist::class)->group(function () {
    Route::post('/booking/notification', [BookingController::class, 'handleNotification'])->name('bookings.notification');
});
