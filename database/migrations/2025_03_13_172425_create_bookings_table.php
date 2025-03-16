<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->date('booking_date');
            $table->string('console_type'); // 'PS4' atau 'PS5'
            $table->integer('session_count')->default(1);
            $table->decimal('base_price', 10, 2);
            $table->decimal('weekend_surcharge', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('payment_status')->default('pending');
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
