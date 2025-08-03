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
        Schema::create('bank_transfer_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('payment_reference')->unique();
            $table->decimal('amount', 10, 2); // Total amount to be paid
            $table->decimal('amount_paid', 10, 2)->default(0); // Amount actually paid
            $table->decimal('amount_remaining', 10, 2); // Remaining amount to pay
            $table->string('virtual_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->enum('status', ['pending', 'partial', 'completed', 'failed', 'expired'])->default('pending');
            $table->integer('reward_points_earned')->default(0);
            $table->integer('reward_points_rate')->default(1); // Points per ₦100
            $table->decimal('reward_points_threshold', 10, 2)->default(100); // ₦100 per point
            $table->decimal('service_charge_rate', 5, 2)->default(2.00); // 2% service charge
            $table->decimal('service_charge_amount', 10, 2)->default(0);
            $table->json('payment_history')->nullable(); // Track all payment attempts
            $table->datetime('expires_at');
            $table->datetime('paid_at')->nullable();
            $table->text('payment_instructions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['payment_reference', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transfer_payments');
    }
};
