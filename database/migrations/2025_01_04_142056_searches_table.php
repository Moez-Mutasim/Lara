<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SearchesTable extends Migration
{
    public function up()
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->bigIncrements('search_id');
            $table->unsignedBigInteger('user_id');
            $table->string('search_type');
            $table->json('search_details');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('searches');
    }
}
