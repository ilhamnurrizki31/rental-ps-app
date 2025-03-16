<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        // Ambil daftar booking yang sudah ada untuk ditampilkan di kalender
        $bookings = Booking::where('payment_status', 'success')
            ->get(['id', 'booking_date', 'console_type'])
            ->map(function ($booking) {
                return [
                    'title' => $booking->console_type . ' Booked',
                    'start' => $booking->booking_date->format('Y-m-d'),
                    'backgroundColor' => $booking->console_type == 'PS4' ? '#3788d8' : '#d88037',
                ];
            });

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $selectedDate = $request->query('date', now()->format('Y-m-d'));
        return view('bookings.create', compact('selectedDate'));
    }

    public function calculatePrice(Request $request)
    {
        $consoleType = $request->input('console_type');
        $bookingDate = Carbon::parse($request->input('booking_date'));
        $sessionCount = $request->input('session_count', 1);

        // Harga dasar
        $basePrice = $consoleType === 'PS5' ? 40000 : 30000;
        $basePrice *= $sessionCount;

        // Weekend surcharge
        $weekendSurcharge = 0;
        if ($bookingDate->isWeekend()) {
            $weekendSurcharge = 50000;
        }

        $totalPrice = $basePrice + $weekendSurcharge;

        return response()->json([
            'base_price' => $basePrice,
            'weekend_surcharge' => $weekendSurcharge,
            'total_price' => $totalPrice,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'console_type' => 'required|in:PS4,PS5',
            'session_count' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        $bookingDate = Carbon::parse($validated['booking_date']);

        // Hitung harga
        $basePrice = $validated['console_type'] === 'PS5' ? 40000 : 30000;
        $basePrice *= $validated['session_count'];

        $weekendSurcharge = 0;
        if ($bookingDate->isWeekend()) {
            $weekendSurcharge = 50000;
        }

        $totalPrice = $basePrice + $weekendSurcharge;

        // Buat booking
        $booking = Booking::create([
            'booking_date' => $validated['booking_date'],
            'console_type' => $validated['console_type'],
            'session_count' => $validated['session_count'],
            'base_price' => $basePrice,
            'weekend_surcharge' => $weekendSurcharge,
            'total_price' => $totalPrice,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
        ]);

        // Buat transaksi Midtrans
        $midtransResponse = $this->midtransService->createTransaction($booking);


        if (isset($midtransResponse['error'])) {
            return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $midtransResponse['error']);
        }

        // Update booking dengan ID transaksi Midtrans
        $booking->update([
            'midtrans_transaction_id' => $midtransResponse['midtrans_transaction_id'],
        ]);

        return view('bookings.payment', [
            'booking' => $booking,
            'snap_token' => $midtransResponse['snap_token'],
        ]);
    }

    public function handleNotification()
    {
        $notificationBody = file_get_contents('php://input');
        $notification = json_decode($notificationBody);

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $paymentType = $notification->payment_type;

        // Extract booking ID from order_id format: BOOK-{id}-{timestamp}
        preg_match('/BOOK-(\d+)-/', $orderId, $matches);
        $bookingId = $matches[1] ?? null;

        if (!$bookingId) {
            return response('Booking ID not found', 404);
        }

        $booking = Booking::find($bookingId);

        if (!$booking) {
            return response('Booking not found', 404);
        }

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            $booking->update([
                'payment_status' => 'success',
                'payment_method' => $paymentType,
                'paid_at' => now(),
            ]);
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $booking->update([
                'payment_status' => 'failed',
            ]);
        } elseif ($transactionStatus == 'pending') {
            $booking->update([
                'payment_status' => 'pending',
            ]);
        }

        return response('OK', 200);
    }

    public function success(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $booking = Booking::findOrFail($bookingId);



        return view('bookings.success', compact('booking'));
    }
}