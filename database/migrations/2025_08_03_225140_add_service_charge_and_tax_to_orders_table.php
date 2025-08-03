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
            $table->decimal('subtotal', 10, 2)->after('total_amount')->default(0); // Amount before charges
            $table->decimal('service_charge', 10, 2)->after('subtotal')->default(0); // Service charge amount
            $table->decimal('tax_amount', 10, 2)->after('service_charge')->default(0); // Tax amount
            $table->decimal('delivery_fee', 10, 2)->after('tax_amount')->default(0); // Delivery fee
            $table->decimal('discount_amount', 10, 2)->after('delivery_fee')->default(0); // Discount amount
            $table->string('discount_code')->after('discount_amount')->nullable(); // Applied discount code
            $table->json('charge_breakdown')->after('discount_code')->nullable(); // Detailed breakdown of charges
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'service_charge', 
                'tax_amount',
                'delivery_fee',
                'discount_amount',
                'discount_code',
                'charge_breakdown'
            ]);
        });
    }
};
