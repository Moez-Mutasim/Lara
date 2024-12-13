<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('country_id');
            $table->string('name');
            $table->string('code');
            $table->string('iso_alpha_3', 3)->unique()->nullable();
            $table->string('continent')->nullable();
            $table->string('currency')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    
    public function down()
    {Schema::dropIfExists('countries');}
}
