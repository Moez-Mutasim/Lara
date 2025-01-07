<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('name', 255);
            $table->string('email', 255)->unique()->nullable();
            $table->string('phone', 20)->unique()->nullable();
            $table->string('password');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('profile_picture')->nullable();
            $table->enum('role', ['agent', 'admin'])->default('agent');
            $table->unsignedBigInteger('country_id')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->boolean('phone_verified')->default(false);
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('country_id')->references('country_id')->on('countries')->onDelete('set null');

            $table->index('email');
            $table->index('phone');
            $table->index('role');
        });
    }

    public function down()
    {Schema::dropIfExists('users');}
}
