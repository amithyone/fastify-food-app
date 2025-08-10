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
        Schema::create('restaurant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->enum('plan_type', ['small', 'normal', 'premium'])->default('small');
            $table->enum('status', ['active', 'expired', 'cancelled', 'trial'])->default('trial');
            $table->date('trial_ends_at')->nullable();
            $table->date('subscription_ends_at')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->integer('menu_item_limit')->default(5);
            $table->boolean('custom_domain_enabled')->default(false);
            $table->boolean('unlimited_menu_items')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('advanced_analytics')->default(false);
            $table->boolean('video_packages_enabled')->default(false);
            $table->boolean('social_media_promotion_enabled')->default(false);
            $table->json('features')->nullable();
            $table->timestamps();
            
            $table->index(['restaurant_id', 'status']);
            $table->index(['plan_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_subscriptions');
    }
};
