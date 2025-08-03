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
        Schema::create('featured_restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable(); // Custom title for the ad
            $table->text('description')->nullable(); // Custom description for the ad
            $table->string('ad_image')->nullable(); // Custom ad image
            $table->string('cta_text')->default('Order Now'); // Call to action text
            $table->string('cta_link')->nullable(); // Custom CTA link
            $table->string('badge_text')->nullable(); // e.g., "New", "Popular", "Limited Time"
            $table->string('badge_color')->default('orange'); // Badge color
            $table->integer('sort_order')->default(0); // For ordering featured restaurants
            $table->boolean('is_active')->default(true);
            $table->timestamp('featured_from')->nullable(); // When to start featuring
            $table->timestamp('featured_until')->nullable(); // When to stop featuring
            $table->integer('click_count')->default(0); // Track clicks
            $table->integer('impression_count')->default(0); // Track impressions
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['is_active', 'featured_from', 'featured_until'], 'featured_active_dates_idx');
            $table->index('sort_order', 'featured_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_restaurants');
    }
};
