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
        Schema::create('social_media_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('campaign_name');
            $table->text('description');
            $table->enum('platform', ['instagram', 'facebook', 'twitter', 'tiktok', 'youtube', 'all']);
            $table->enum('status', ['draft', 'pending', 'active', 'completed', 'cancelled'])->default('draft');
            $table->decimal('budget', 10, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->json('target_audience')->nullable();
            $table->json('content_plan')->nullable();
            $table->string('hashtags')->nullable();
            $table->string('call_to_action')->nullable();
            $table->string('landing_page_url')->nullable();
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('engagements')->default(0);
            $table->decimal('roi', 5, 2)->default(0);
            $table->timestamps();
            
            $table->index(['restaurant_id', 'status']);
            $table->index(['platform', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_campaigns');
    }
};

