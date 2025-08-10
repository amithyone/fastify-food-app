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
        // First, ensure subscription_payments table exists
        if (!Schema::hasTable('subscription_payments')) {
            Schema::create('subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('payment_reference')->unique();
                $table->string('plan_type'); // small, normal, premium
                $table->decimal('amount', 10, 2);
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->enum('status', ['pending', 'successful', 'failed', 'expired'])->default('pending');
                $table->string('virtual_account_number')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('account_name')->nullable();
                $table->string('gateway_reference')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->index(['restaurant_id', 'status']);
                $table->index(['payment_reference']);
                $table->index(['status', 'expires_at']);
            });
        }

        // Ensure subscription_plans table exists
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
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

        // Ensure restaurant_subscriptions table exists
        if (!Schema::hasTable('restaurant_subscriptions')) {
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

        // Now update pay_vibe_transactions table if it exists
        if (Schema::hasTable('pay_vibe_transactions')) {
            // Drop existing foreign key if it exists
            try {
                Schema::table('pay_vibe_transactions', function (Blueprint $table) {
                    $table->dropForeign(['payment_id']);
                });
            } catch (Exception $e) {
                // Foreign key doesn't exist, continue
            }

            // Add new columns if they don't exist
            if (!Schema::hasColumn('pay_vibe_transactions', 'payment_type')) {
                Schema::table('pay_vibe_transactions', function (Blueprint $table) {
                    $table->enum('payment_type', ['promotion', 'subscription'])->default('promotion')->after('payment_id');
                });
            }

            if (!Schema::hasColumn('pay_vibe_transactions', 'subscription_payment_id')) {
                Schema::table('pay_vibe_transactions', function (Blueprint $table) {
                    $table->unsignedBigInteger('subscription_payment_id')->nullable()->after('payment_id');
                });
            }

            // Add foreign key for subscription payments
            try {
                Schema::table('pay_vibe_transactions', function (Blueprint $table) {
                    $table->foreign('subscription_payment_id')->references('id')->on('subscription_payments')->onDelete('cascade');
                });
            } catch (Exception $e) {
                // Foreign key already exists, continue
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key if it exists
        if (Schema::hasTable('pay_vibe_transactions')) {
            try {
                Schema::table('pay_vibe_transactions', function (Blueprint $table) {
                    $table->dropForeign(['subscription_payment_id']);
                });
            } catch (Exception $e) {
                // Foreign key doesn't exist, continue
            }

            // Drop the columns
            Schema::table('pay_vibe_transactions', function (Blueprint $table) {
                $table->dropColumn(['subscription_payment_id', 'payment_type']);
            });
        }

        // Drop the tables in reverse order
        Schema::dropIfExists('restaurant_subscriptions');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('subscription_payments');
    }
};
