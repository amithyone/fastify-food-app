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
        Schema::create('pay_vibe_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->string('reference')->unique();
            $table->integer('amount'); // Amount in kobo
            $table->enum('status', ['pending', 'successful', 'failed'])->default('pending');
            $table->text('authorization_url')->nullable();
            $table->string('access_code')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->integer('amount_received')->nullable(); // Amount received in kobo
            $table->json('metadata')->nullable();
            $table->json('webhook_data')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('payment_id')->references('id')->on('promotion_payments')->onDelete('cascade');

            // Indexes
            $table->index(['reference']);
            $table->index(['status']);
            $table->index(['payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_vibe_transactions');
    }
};
