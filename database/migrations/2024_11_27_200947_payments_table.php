<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('payment_id');
            $table->unsignedBigInteger('booking_id');
            $table->string('payment_reference')->unique();
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount', 15, 2);
            $table->decimal('transaction_fee', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');

            $table->index('booking_id');
            $table->index('payment_status');
        });
    }

    public function down()
    {Schema::dropIfExists('payments');}
}
