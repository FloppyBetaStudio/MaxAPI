<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('token');
            $table->dateTime('dt_create');
            $table->dateTime('dt_expire');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_access_tokens');
    }
};
