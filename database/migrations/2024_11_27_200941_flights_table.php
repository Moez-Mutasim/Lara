<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FlightsTable extends Migration
{
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->bigIncrements('flight_id');
            $table->unsignedBigInteger('departure_id');
            $table->unsignedBigInteger('destination_id');
            $table->string('airline_name');
            $table->timestamp('departure_time');
            $table->timestamp('arrival_time');
            $table->string('duration')->nullable();
            $table->integer('seats_available')->default(0);
            $table->enum('class', ['Economy', 'Business', 'First'])->default('Economy');
            $table->decimal('price', 10, 2);
            $table->boolean('is_available')->default(true);
            $table->string('flight_image')->nullable();
            $table->timestamps();

            
            $table->foreign('departure_id')->references('location_id')->on('locations')->onDelete('cascade');
            $table->foreign('destination_id')->references('location_id')->on('locations')->onDelete('cascade');
        });
    }

    public function down()
    {Schema::dropIfExists('flights');}
}
