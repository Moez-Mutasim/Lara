<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('review_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('flight_id')->nullable();
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->decimal('rating', 2, 1)->nullable()->default(null);
            $table->text('comment')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('flight_id')->references('flight_id')->on('flights')->onDelete('cascade');
            $table->foreign('hotel_id')->references('hotel_id')->on('hotels')->onDelete('cascade');
            $table->foreign('car_id')->references('car_id')->on('cars')->onDelete('cascade');

            $table->index('user_id');
            $table->index('flight_id');
            $table->index('hotel_id');
            $table->index('car_id');
            $table->index('rating');
            $table->index('is_verified');
        });
    }

    public function down()
    {Schema::dropIfExists('reviews');}
}
