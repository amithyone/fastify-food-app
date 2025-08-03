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
            $table->enum('order_type', ['delivery', 'pickup', 'in_restaurant'])->after('delivery_time')->default('delivery');
            $table->string('pickup_code')->after('order_type')->nullable();
            $table->datetime('pickup_time')->after('pickup_code')->nullable();
            $table->string('pickup_name')->after('pickup_time')->nullable();
            $table->string('pickup_phone')->after('pickup_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_type',
                'pickup_code',
                'pickup_time',
                'pickup_name',
                'pickup_phone'
            ]);
        });
    }
};
