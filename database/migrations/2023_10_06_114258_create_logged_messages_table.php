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
            $table->enum('event_type', ['update', 'create', 'delete']);
            $table->string('model');
            $table->text('original_data')->nullable();
            $table->text('new_data')->nullable();
            $table->text('app_id');
            $table->string('route');
            $table->string('remote_user_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address');
            $table->timestamp('date')->useCurrent();
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
