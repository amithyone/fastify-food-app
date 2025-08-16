<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix the managers table id field to ensure it's auto-incrementing
        try {
            DB::statement('ALTER TABLE managers MODIFY id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY');
        } catch (Exception $e) {
            // If the table doesn't exist or already has the correct structure, continue
        }
    }

    public function down(): void
    {
        // No need to reverse this as it's just fixing the structure
    }
};
