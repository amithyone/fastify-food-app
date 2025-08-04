<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🧹 Clearing all restaurant and user data...\n\n";

try {
    // Start transaction
    DB::beginTransaction();
    
    echo "1️⃣ Clearing orders and related data...\n";
    
    // Clear order items first (foreign key constraint)
    DB::table('order_items')->truncate();
    echo "   ✅ Order items cleared\n";
    
    // Clear orders
    DB::table('orders')->truncate();
    echo "   ✅ Orders cleared\n";
    
    // Clear bank transfer payments
    DB::table('bank_transfer_payments')->truncate();
    echo "   ✅ Bank transfer payments cleared\n";
    
    // Clear pay vibe transactions
    DB::table('pay_vibe_transactions')->truncate();
    echo "   ✅ PayVibe transactions cleared\n";
    
    echo "\n2️⃣ Clearing restaurant data...\n";
    
    // Clear restaurant delivery settings
    DB::table('restaurant_delivery_settings')->truncate();
    echo "   ✅ Restaurant delivery settings cleared\n";
    
    // Clear menu item delivery methods
    DB::table('menu_item_delivery_methods')->truncate();
    echo "   ✅ Menu item delivery methods cleared\n";
    
    // Clear menu items
    DB::table('menu_items')->truncate();
    echo "   ✅ Menu items cleared\n";
    
    // Clear categories
    DB::table('categories')->truncate();
    echo "   ✅ Categories cleared\n";
    
    // Clear stories
    DB::table('stories')->truncate();
    echo "   ✅ Stories cleared\n";
    
    // Clear restaurant ratings
    DB::table('restaurant_ratings')->truncate();
    echo "   ✅ Restaurant ratings cleared\n";
    
    // Clear table QR codes
    DB::table('table_q_r_s')->truncate();
    echo "   ✅ Table QR codes cleared\n";
    
    // Clear featured restaurants
    DB::table('featured_restaurants')->truncate();
    echo "   ✅ Featured restaurants cleared\n";
    
    // Clear restaurants
    DB::table('restaurants')->truncate();
    echo "   ✅ Restaurants cleared\n";
    
    echo "\n3️⃣ Clearing user data...\n";
    
    // Clear user rewards
    DB::table('user_rewards')->truncate();
    echo "   ✅ User rewards cleared\n";
    
    // Clear wallet transactions
    DB::table('wallet_transactions')->truncate();
    echo "   ✅ Wallet transactions cleared\n";
    
    // Clear wallets
    DB::table('wallets')->truncate();
    echo "   ✅ Wallets cleared\n";
    
    // Clear addresses
    DB::table('addresses')->truncate();
    echo "   ✅ Addresses cleared\n";
    
    // Clear phone verifications
    DB::table('phone_verifications')->truncate();
    echo "   ✅ Phone verifications cleared\n";
    
    // Clear guest sessions
    DB::table('guest_sessions')->truncate();
    echo "   ✅ Guest sessions cleared\n";
    
    // Clear managers
    DB::table('managers')->truncate();
    echo "   ✅ Managers cleared\n";
    
    // Clear users (except admin)
    DB::table('users')->where('is_admin', false)->delete();
    echo "   ✅ Regular users cleared (admin preserved)\n";
    
    echo "\n4️⃣ Clearing promotion data...\n";
    
    // Clear promotion payments
    DB::table('promotion_payments')->truncate();
    echo "   ✅ Promotion payments cleared\n";
    
    // Clear promotion plans
    DB::table('promotion_plans')->truncate();
    echo "   ✅ Promotion plans cleared\n";
    
    // Clear payment settings
    DB::table('payment_settings')->truncate();
    echo "   ✅ Payment settings cleared\n";
    
    // Commit transaction
    DB::commit();
    
    echo "\n🎉 All data cleared successfully!\n";
    echo "\n📋 Next steps:\n";
    echo "1. Run: php artisan db:seed\n";
    echo "2. Run: php artisan migrate:fresh --seed\n";
    echo "3. Or run individual seeders:\n";
    echo "   - php artisan db:seed --class=PaymentSettingsSeeder\n";
    echo "   - php artisan db:seed --class=SampleRestaurantSeeder\n";
    
} catch (Exception $e) {
    // Rollback transaction on error
    DB::rollBack();
    echo "❌ Error clearing data: " . $e->getMessage() . "\n";
    exit(1);
} 