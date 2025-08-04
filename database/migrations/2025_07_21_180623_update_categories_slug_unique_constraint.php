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
            // Check if the global slug unique constraint exists before dropping it
            $globalIndexExists = collect(\DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_slug_unique'"))->isNotEmpty();
            
            if ($globalIndexExists) {
                // Drop the existing unique constraint
                $table->dropUnique(['slug']);
            }
            
            // Check if the restaurant-specific constraint already exists
            $restaurantIndexExists = collect(\DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_slug_restaurant_unique'"))->isNotEmpty();
            
            if (!$restaurantIndexExists) {
                // Add unique constraint for slug per restaurant
                $table->unique(['slug', 'restaurant_id'], 'categories_slug_restaurant_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Check if the index exists before trying to drop it
            $indexExists = collect(\DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_slug_restaurant_unique'"))->isNotEmpty();
            
            if ($indexExists) {
                // Drop the restaurant-specific unique constraint
                $table->dropUnique('categories_slug_restaurant_unique');
            }
            
            // Check if the global slug unique constraint exists
            $globalIndexExists = collect(\DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_slug_unique'"))->isNotEmpty();
            
            if (!$globalIndexExists) {
                // Restore the global unique constraint
                $table->unique(['slug'], 'categories_slug_unique');
            }
        });
    }
}; 