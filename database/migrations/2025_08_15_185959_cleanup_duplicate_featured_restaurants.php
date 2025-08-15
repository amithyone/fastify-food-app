<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean up duplicate featured restaurant entries
        // Keep only the most recent entry for each restaurant
        $duplicates = DB::table('featured_restaurants')
            ->select('restaurant_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('restaurant_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            // Delete all entries except the most recent one
            DB::table('featured_restaurants')
                ->where('restaurant_id', $duplicate->restaurant_id)
                ->where('id', '!=', $duplicate->max_id)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as it deletes data
        // We'll leave it empty to prevent accidental data loss
    }
};
