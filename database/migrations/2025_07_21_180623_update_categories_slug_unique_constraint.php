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
        Schema::table('categories', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique(['slug']);
            
            // Add unique constraint for slug per restaurant
            $table->unique(['slug', 'restaurant_id'], 'categories_slug_restaurant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop the restaurant-specific unique constraint
            $table->dropUnique('categories_slug_restaurant_unique');
            
            // Restore the global unique constraint
            $table->unique(['slug']);
        });
    }
}; 