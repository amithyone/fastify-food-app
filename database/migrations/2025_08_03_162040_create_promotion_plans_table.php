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
        Schema::create('promotion_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Basic", "Premium", "Featured"
            $table->string('slug')->unique(); // e.g., "basic", "premium", "featured"
            $table->text('description');
            $table->integer('price'); // Price in kobo (smallest currency unit)
            $table->integer('duration_days'); // How long the promotion lasts
            $table->integer('max_impressions')->nullable(); // Maximum impressions allowed
            $table->json('features'); // Array of features included
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_plans');
    }
};
