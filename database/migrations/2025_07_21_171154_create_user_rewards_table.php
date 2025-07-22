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
        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('guest_session_id')->nullable();
            $table->integer('points')->default(0);
            $table->integer('total_spent')->default(0);
            $table->integer('orders_count')->default(0);
            $table->string('tier')->default('bronze'); // bronze, silver, gold, platinum
            $table->json('rewards_earned')->nullable();
            $table->json('rewards_redeemed')->nullable();
            $table->timestamp('last_order_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'restaurant_id']);
            $table->unique(['guest_session_id', 'restaurant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_rewards');
    }
};
