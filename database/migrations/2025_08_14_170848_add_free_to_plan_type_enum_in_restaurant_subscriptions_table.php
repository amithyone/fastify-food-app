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
        // Add 'free' to the plan_type enum
        DB::statement("ALTER TABLE restaurant_subscriptions MODIFY COLUMN plan_type ENUM('small', 'normal', 'premium', 'free') NOT NULL DEFAULT 'small'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'free' from the plan_type enum
        DB::statement("ALTER TABLE restaurant_subscriptions MODIFY COLUMN plan_type ENUM('small', 'normal', 'premium') NOT NULL DEFAULT 'small'");
    }
};
