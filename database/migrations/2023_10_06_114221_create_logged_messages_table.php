<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logged_messages', function (Blueprint $table) {
            $table->id();
            $table->enum('event_name', ['update', 'create', 'delete']);
            $table->binary('original_data')->nullable();
            $table->binary('new_data')->nullable();
            $table->binary('user_data');
            $table->string('route');
            $table->string('ip_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logged_messages');
    }
};