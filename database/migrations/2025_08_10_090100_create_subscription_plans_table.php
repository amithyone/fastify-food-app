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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Small Restaurant", "Normal Restaurant", "Premium Restaurant"
            $table->string('slug')->unique(); // e.g., "small", "normal", "premium"
            $table->text('description');
            $table->decimal('monthly_price', 10, 2);
            $table->integer('menu_item_limit')->default(5);
            $table->boolean('custom_domain_enabled')->default(false);
            $table->boolean('unlimited_menu_items')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('advanced_analytics')->default(false);
            $table->boolean('video_packages_enabled')->default(false);
            $table->boolean('social_media_promotion_enabled')->default(false);
            $table->json('features')->nullable();
            $table->json('limitations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('color_scheme')->default('blue');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
