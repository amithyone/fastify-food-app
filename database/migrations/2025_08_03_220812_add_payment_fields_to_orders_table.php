<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check if columns already exist before adding them
            if (!Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'gateway_reference')) {
                $table->string('gateway_reference')->nullable()->after('payment_reference');
            }
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
            
            // Only modify payment_status if it doesn't already have the correct enum values
            if (Schema::hasColumn('orders', 'payment_status')) {
                // Check if the column is already the correct enum type
                $columnInfo = DB::select("SHOW COLUMNS FROM orders WHERE Field = 'payment_status'")[0];
                if (strpos($columnInfo->Type, "enum('pending','completed','failed','cancelled')") === false) {
                    $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending')->change();
                }
            } else {
                $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending')->after('gateway_reference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_reference',
                'gateway_reference', 
                'payment_status',
                'paid_at'
            ]);
        });
    }
};
