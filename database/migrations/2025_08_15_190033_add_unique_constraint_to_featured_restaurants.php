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
        Schema::table('featured_restaurants', function (Blueprint $table) {
            // Add unique constraint on restaurant_id to prevent duplicates
            $table->unique('restaurant_id', 'featured_restaurants_restaurant_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('featured_restaurants', function (Blueprint $table) {
            $table->dropUnique('featured_restaurants_restaurant_id_unique');
        });
    }
};
