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
        Schema::create('video_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('package_name');
            $table->text('description');
            $table->enum('package_type', ['basic', 'premium', 'custom']);
            $table->enum('status', ['pending', 'in_production', 'completed', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('price', 10, 2);
            $table->integer('video_duration')->comment('Duration in seconds');
            $table->integer('number_of_videos')->default(1);
            $table->json('video_requirements')->nullable();
            $table->json('deliverables')->nullable();
            $table->date('shoot_date')->nullable();
            $table->time('shoot_time')->nullable();
            $table->string('location_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('special_instructions')->nullable();
            $table->string('video_file_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('social_media_links')->nullable();
            $table->integer('views')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('engagements')->default(0);
            $table->timestamps();
            
            $table->index(['restaurant_id', 'status']);
            $table->index(['package_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_packages');
    }
};
