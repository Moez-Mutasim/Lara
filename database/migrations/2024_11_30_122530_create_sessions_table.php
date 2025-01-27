<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable()->default(null);
            $table->text('user_customer')->nullable()->default(null);
            $table->longText('payload');
            $table->integer('last_activity')->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {Schema::dropIfExists('sessions');}
};
