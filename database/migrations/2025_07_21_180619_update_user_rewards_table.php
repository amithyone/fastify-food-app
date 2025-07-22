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
        Schema::table('user_rewards', function (Blueprint $table) {
            // Add missing columns for the reward system
            $table->foreignId('order_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            $table->integer('points_earned')->after('points')->default(0);
            $table->decimal('order_amount', 10, 2)->after('points_earned')->default(0);
            $table->string('payment_method')->after('order_amount')->nullable();
            $table->enum('status', ['pending', 'credited', 'expired'])->after('payment_method')->default('pending');
            $table->timestamp('credited_at')->nullable()->after('status');
            $table->timestamp('expires_at')->nullable()->after('credited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_rewards', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn([
                'order_id',
                'points_earned',
                'order_amount',
                'payment_method',
                'status',
                'credited_at',
                'expires_at'
            ]);
        });
    }
};
