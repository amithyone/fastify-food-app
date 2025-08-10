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
        Schema::table('pay_vibe_transactions', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['payment_id']);
            
            // Make payment_id nullable and add payment_type
            $table->unsignedBigInteger('payment_id')->nullable()->change();
            $table->enum('payment_type', ['promotion', 'subscription'])->default('promotion')->after('payment_id');
            
            // Add subscription_payment_id for subscription payments
            $table->unsignedBigInteger('subscription_payment_id')->nullable()->after('payment_id');
            
            // Add foreign key for subscription payments
            $table->foreign('subscription_payment_id')->references('id')->on('subscription_payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pay_vibe_transactions', function (Blueprint $table) {
            // Drop subscription payment foreign key
            $table->dropForeign(['subscription_payment_id']);
            $table->dropColumn(['subscription_payment_id', 'payment_type']);
            
            // Restore original foreign key constraint
            $table->unsignedBigInteger('payment_id')->nullable(false)->change();
            $table->foreign('payment_id')->references('id')->on('promotion_payments')->onDelete('cascade');
        });
    }
};
