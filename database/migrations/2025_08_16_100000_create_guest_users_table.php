<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->unique(); // Reduced length to avoid index issues
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('session_token', 191)->unique()->nullable(); // For QR code tracking
            $table->timestamp('session_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            
            // Single column indexes to avoid composite index length issues
            $table->index('email');
            $table->index('is_active');
            $table->index('session_token');
            $table->index('session_expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_users');
    }
};
