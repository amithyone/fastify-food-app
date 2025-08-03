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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'service_charge', 'tax_rate', 'delivery_fee'
            $table->string('name'); // e.g., 'Service Charge', 'Tax Rate', 'Delivery Fee'
            $table->text('description')->nullable();
            $table->decimal('value', 10, 4)->default(0); // The actual value (percentage or fixed amount)
            $table->enum('type', ['percentage', 'fixed'])->default('percentage'); // percentage or fixed amount
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('conditions')->nullable(); // Additional conditions (e.g., minimum order amount)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
