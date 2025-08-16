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
        // Fix the categories table id field to ensure it's auto-incrementing
        try {
            DB::statement('ALTER TABLE categories MODIFY id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY');
        } catch (Exception $e) {
            // If the table doesn't exist or already has the correct structure, continue
        }

        // Fix the subscription_plans table id field as well (from the error logs)
        try {
            DB::statement('ALTER TABLE subscription_plans MODIFY id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY');
        } catch (Exception $e) {
            // If the table doesn't exist or already has the correct structure, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this as it's just fixing the structure
    }
};
