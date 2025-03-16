<?php

namespace App\Services;

use App\Models\Booking;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction(Booking $booking)
    {
        $transactionDetails = [
            'order_id' => 'BOOK-' . $booking->id . '-' . time(),
            'gross_amount' => (int) $booking->total_price,
        ];

        $customerDetails = [
            'first_name' => $booking->customer_name,
            'email' => $booking->customer_email,
            'phone' => $booking->customer_phone,
        ];

        $itemDetails = [
            [
                'id' => $booking->console_type,
                'price' => (int) $booking->base_price,
                'quantity' => $booking->session_count,
                'name' => 'Rental ' . $booking->console_type . ' (' . $booking->session_count . ' sesi)',
            ]
        ];

        // Tambahkan weekend surcharge jika ada
        if ($booking->weekend_surcharge > 0) {
            $itemDetails[] = [
                'id' => 'WEEKEND-SURCHARGE',
                'price' => (int) $booking->weekend_surcharge,
                'quantity' => 1,
                'name' => 'Weekend Surcharge',
            ];
        }

        $params = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return [
                'snap_token' => $snapToken,
                'midtrans_transaction_id' => $transactionDetails['order_id'],
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
