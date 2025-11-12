<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('payment_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'digital']);
            $table->string('digital_type')->nullable()->comment('bank_transfer, credit_card, debit_card, e_wallet');
            $table->string('transaction_id')->nullable()->comment('For digital payments');
            $table->string('screenshot_path')->nullable()->comment('For digital payments proof');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->date('payment_date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
