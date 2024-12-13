<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CarsTable extends Migration
{
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->bigIncrements('car_id');
            $table->year('model')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('rental_price', 15, 2)->nullable();
            $table->boolean('availability')->default(true);
            $table->json('features')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('branch_id')->on('branches')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();

            $table->index('brand');
            $table->index('availability');
        });
    }

    public function down()
    {Schema::dropIfExists('cars');}
}
