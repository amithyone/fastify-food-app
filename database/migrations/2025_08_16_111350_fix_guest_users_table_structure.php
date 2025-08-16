<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_users', function (Blueprint $table) {
            // Drop existing indexes first
            $table->dropIndex(['email', 'is_active']);
            $table->dropIndex(['session_token', 'session_expires_at']);
            
            // Modify email column to 191 characters
            $table->string('email', 191)->change();
            
            // Modify session_token column to 191 characters
            $table->string('session_token', 191)->change();
            
            // Add single column indexes
            $table->index('email');
            $table->index('is_active');
            $table->index('session_token');
            $table->index('session_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('guest_users', function (Blueprint $table) {
            // Drop single column indexes
            $table->dropIndex(['email']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['session_token']);
            $table->dropIndex(['session_expires_at']);
            
            // Revert column lengths
            $table->string('email', 255)->change();
            $table->string('session_token', 255)->change();
            
            // Re-add composite indexes
            $table->index(['email', 'is_active']);
            $table->index(['session_token', 'session_expires_at']);
        });
    }
};
