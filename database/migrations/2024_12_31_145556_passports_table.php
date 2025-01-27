<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PassportsTable extends Migration
{
    public function up()
    {
        Schema::create('passports', function (Blueprint $table) {
            $table->bigIncrements('passport_id');
            $table->unsignedBigInteger('user_id');
            $table->string('passport_number')->unique();
            $table->string('full_name');
            $table->string('country_of_issue');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('passport_image')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

            $table->index('user_id');
            $table->index('passport_number');
            $table->index('is_verified');
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('passports');
    }
}
