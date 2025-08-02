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
        Schema::create('managers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['owner', 'manager', 'staff'])->default('manager');
            $table->boolean('is_active')->default(true);
            $table->json('permissions')->nullable(); // Store specific permissions
            $table->timestamp('last_access_at')->nullable();
            $table->timestamps();
            
            // Ensure a user can only have one role per restaurant
            $table->unique(['user_id', 'restaurant_id']);
            
            // Index for quick lookups
            $table->index(['restaurant_id', 'role']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
