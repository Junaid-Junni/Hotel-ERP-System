<?php
// database/migrations/2024_01_01_000002_create_bookings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->string('guest_address')->nullable();
            $table->integer('adults');
            $table->integer('children')->default(0);
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('total_nights');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['Pending', 'Paid', 'Partial', 'Refunded'])->default('Pending');
            $table->enum('status', ['Confirmed', 'Checked In', 'Checked Out', 'Cancelled'])->default('Confirmed');
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
