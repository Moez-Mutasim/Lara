<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('booking_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('flight_id')->nullable();
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->decimal('total_price', 15, 2);
            $table->date('booking_date')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('flight_id')->references('flight_id')->on('flights')->onDelete('cascade');
            $table->foreign('hotel_id')->references('hotel_id')->on('hotels')->onDelete('cascade');
            $table->foreign('car_id')->references('car_id')->on('cars')->onDelete('cascade');


            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
