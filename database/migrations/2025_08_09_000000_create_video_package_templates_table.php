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
        Schema::create('video_package_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Basic", "Premium", "Custom"
            $table->string('slug')->unique(); // e.g., "basic", "premium", "custom"
            $table->text('description');
            $table->decimal('base_price', 10, 2); // Base price for the package
            $table->integer('video_duration')->comment('Duration in seconds');
            $table->integer('number_of_videos')->default(1);
            $table->json('features'); // Array of features included
            $table->json('deliverables'); // What the customer gets
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('color_scheme')->default('blue'); // For UI styling
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_package_templates');
    }
};
