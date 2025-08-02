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
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'restaurant_id')) {
                $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('wallets', 'currency')) {
                $table->string('currency', 10)->default('₦');
            }
            if (!Schema::hasColumn('wallets', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            if (Schema::hasColumn('wallets', 'restaurant_id')) {
                $table->dropForeign(['restaurant_id']);
                $table->dropColumn('restaurant_id');
            }
            if (Schema::hasColumn('wallets', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('wallets', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
