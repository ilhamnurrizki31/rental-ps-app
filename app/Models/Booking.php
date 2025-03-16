<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_date',
        'console_type',
        'session_count',
        'base_price',
        'weekend_surcharge',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_status',
        'midtrans_transaction_id',
        'payment_method',
        'paid_at'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'paid_at' => 'datetime',
    ];
}
