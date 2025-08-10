<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing Live Server Subscription Migration...\n\n";

try {
    // Check if subscription_payments table exists
    if (!Schema::hasTable('subscription_payments')) {
        echo "❌ subscription_payments table does not exist. Creating it first...\n";
        
        // Create subscription_payments table
        Schema::create('subscription_payments', function ($table) {
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
        
        echo "✅ subscription_payments table created successfully!\n";
    } else {
        echo "✅ subscription_payments table already exists.\n";
    }
    
    // Check if pay_vibe_transactions table exists
    if (!Schema::hasTable('pay_vibe_transactions')) {
        echo "❌ pay_vibe_transactions table does not exist. Creating it...\n";
        
        // Create pay_vibe_transactions table
        Schema::create('pay_vibe_transactions', function ($table) {
            $table->id();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('subscription_payment_id')->nullable();
            $table->enum('payment_type', ['promotion', 'subscription'])->default('promotion');
            $table->string('reference')->unique();
            $table->integer('amount'); // Amount in kobo
            $table->enum('status', ['pending', 'successful', 'failed'])->default('pending');
            $table->text('authorization_url')->nullable();
            $table->string('access_code')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->integer('amount_received')->nullable(); // Amount received in kobo
            $table->json('metadata')->nullable();
            $table->json('webhook_data')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('payment_id')->references('id')->on('promotion_payments')->onDelete('cascade');
            $table->foreign('subscription_payment_id')->references('id')->on('subscription_payments')->onDelete('cascade');

            // Indexes
            $table->index(['reference']);
            $table->index(['status']);
            $table->index(['payment_id']);
            $table->index(['subscription_payment_id']);
        });
        
        echo "✅ pay_vibe_transactions table created successfully!\n";
    } else {
        echo "✅ pay_vibe_transactions table exists. Checking if it needs updates...\n";
        
        // Check if the new columns exist
        $hasPaymentType = Schema::hasColumn('pay_vibe_transactions', 'payment_type');
        $hasSubscriptionPaymentId = Schema::hasColumn('pay_vibe_transactions', 'subscription_payment_id');
        
        if (!$hasPaymentType || !$hasSubscriptionPaymentId) {
            echo "🔄 Updating pay_vibe_transactions table...\n";
            
            // Drop existing foreign key if it exists
            try {
                Schema::table('pay_vibe_transactions', function ($table) {
                    $table->dropForeign(['payment_id']);
                });
            } catch (Exception $e) {
                echo "ℹ️  No existing foreign key to drop.\n";
            }
            
            // Add new columns
            Schema::table('pay_vibe_transactions', function ($table) {
                if (!Schema::hasColumn('pay_vibe_transactions', 'payment_type')) {
                    $table->enum('payment_type', ['promotion', 'subscription'])->default('promotion')->after('payment_id');
                }
                if (!Schema::hasColumn('pay_vibe_transactions', 'subscription_payment_id')) {
                    $table->unsignedBigInteger('subscription_payment_id')->nullable()->after('payment_id');
                }
            });
            
            // Add foreign key for subscription payments
            Schema::table('pay_vibe_transactions', function ($table) {
                $table->foreign('subscription_payment_id')->references('id')->on('subscription_payments')->onDelete('cascade');
            });
            
            echo "✅ pay_vibe_transactions table updated successfully!\n";
        } else {
            echo "✅ pay_vibe_transactions table is already up to date.\n";
        }
    }
    
    // Check if subscription_plans table exists
    if (!Schema::hasTable('subscription_plans')) {
        echo "❌ subscription_plans table does not exist. Creating it...\n";
        
        Schema::create('subscription_plans', function ($table) {
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
        
        echo "✅ subscription_plans table created successfully!\n";
    } else {
        echo "✅ subscription_plans table already exists.\n";
    }
    
    // Check if restaurant_subscriptions table exists
    if (!Schema::hasTable('restaurant_subscriptions')) {
        echo "❌ restaurant_subscriptions table does not exist. Creating it...\n";
        
        Schema::create('restaurant_subscriptions', function ($table) {
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
        
        echo "✅ restaurant_subscriptions table created successfully!\n";
    } else {
        echo "✅ restaurant_subscriptions table already exists.\n";
    }
    
    echo "\n🎉 All subscription-related tables are now properly set up!\n";
    echo "✅ You can now run: php artisan migrate\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
