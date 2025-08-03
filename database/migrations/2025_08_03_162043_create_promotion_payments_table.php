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
        Schema::create('promotion_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('promotion_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('featured_restaurant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('payment_reference')->unique(); // Generated payment reference
            $table->string('account_number')->unique(); // Generated account number for payment
            $table->integer('amount'); // Amount in kobo
            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'card', 'wallet'])->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('payment_details')->nullable(); // Bank details, transaction info, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'expires_at']);
            $table->index('payment_reference');
            $table->index('account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_payments');
    }
};
