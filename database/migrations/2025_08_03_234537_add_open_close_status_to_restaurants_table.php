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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('is_open')->default(true)->after('is_verified');
            $table->time('opening_time')->nullable()->after('is_open');
            $table->time('closing_time')->nullable()->after('opening_time');
            $table->json('weekly_schedule')->nullable()->after('closing_time'); // Store weekly schedule
            $table->text('status_message')->nullable()->after('weekly_schedule'); // Custom message when closed
            $table->boolean('auto_open_close')->default(false)->after('status_message'); // Auto open/close based on time
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'is_open',
                'opening_time',
                'closing_time',
                'weekly_schedule',
                'status_message',
                'auto_open_close'
            ]);
        });
    }
};
