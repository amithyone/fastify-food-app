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
            
            // Modify payment_status column if it exists as varchar
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending')->change();
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
