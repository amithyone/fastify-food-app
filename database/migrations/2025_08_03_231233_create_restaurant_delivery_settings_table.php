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
        Schema::create('restaurant_delivery_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->enum('delivery_mode', ['flexible', 'fixed'])->default('flexible');
            $table->boolean('delivery_enabled')->default(true);
            $table->boolean('pickup_enabled')->default(true);
            $table->boolean('in_restaurant_enabled')->default(true);
            $table->json('delivery_radius')->nullable(); // Delivery radius in km
            $table->decimal('minimum_delivery_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(500);
            $table->integer('delivery_time_minutes')->default(30);
            $table->integer('pickup_time_minutes')->default(20);
            $table->text('delivery_notes')->nullable();
            $table->text('pickup_notes')->nullable();
            $table->timestamps();

            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->unique('restaurant_id');
        });

        Schema::create('menu_item_delivery_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_item_id');
            $table->enum('delivery_method', ['delivery', 'pickup', 'in_restaurant']);
            $table->boolean('enabled')->default(true);
            $table->decimal('additional_fee', 10, 2)->default(0); // Additional fee for this method
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->unique(['menu_item_id', 'delivery_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_delivery_methods');
        Schema::dropIfExists('restaurant_delivery_settings');
    }
};
