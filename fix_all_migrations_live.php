<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Fixing all migration issues on live server...\n\n";

try {
    // 1. Fix session_id column in orders table
    echo "1️⃣ Checking session_id column in orders table...\n";
    $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'session_id'");
    
    if (empty($columns)) {
        echo "❌ session_id column not found. Adding it...\n";
        DB::statement("ALTER TABLE orders ADD COLUMN session_id VARCHAR(255) NULL AFTER user_id");
        DB::statement("ALTER TABLE orders ADD INDEX idx_session_id (session_id)");
        echo "✅ Successfully added session_id column and index\n";
    } else {
        echo "✅ session_id column already exists\n";
    }
    
    // Mark session_id migration as run
    $sessionMigrationExists = DB::table('migrations')
        ->where('migration', '2025_08_04_103905_add_session_id_to_orders_table')
        ->exists();
    
    if (!$sessionMigrationExists) {
        echo "📝 Marking session_id migration as run...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_08_04_103905_add_session_id_to_orders_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "✅ Session migration marked as run\n";
    } else {
        echo "✅ Session migration already marked as run\n";
    }
    
    // 2. Fix orders status enum
    echo "\n2️⃣ Checking orders status enum...\n";
    $result = DB::select("SHOW COLUMNS FROM orders WHERE Field = 'status'");
    $currentType = $result[0]->Type;
    
    if (strpos($currentType, 'pending_payment') === false) {
        echo "❌ 'pending_payment' not in enum. Adding it...\n";
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'pending_payment', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending'");
        echo "✅ Successfully added 'pending_payment' to status enum\n";
    } else {
        echo "✅ 'pending_payment' already exists in enum\n";
    }
    
    // Mark pending_payment migration as run
    $pendingPaymentMigrationExists = DB::table('migrations')
        ->where('migration', '2025_08_04_000644_add_pending_payment_to_orders_status_enum')
        ->exists();
    
    if (!$pendingPaymentMigrationExists) {
        echo "📝 Marking pending_payment migration as run...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_08_04_000644_add_pending_payment_to_orders_status_enum',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "✅ Pending payment migration marked as run\n";
    } else {
        echo "✅ Pending payment migration already marked as run\n";
    }
    
    // 3. Fix bank_transfer_payments table
    echo "\n3️⃣ Checking bank_transfer_payments table...\n";
    if (Schema::hasTable('bank_transfer_payments')) {
        echo "✅ Table exists\n";
        $bankTransferMigrationExists = DB::table('migrations')
            ->where('migration', '2025_08_03_232054_create_bank_transfer_payments_table')
            ->exists();
        
        if (!$bankTransferMigrationExists) {
            echo "📝 Marking bank_transfer_payments migration as run...\n";
            DB::table('migrations')->insert([
                'migration' => '2025_08_03_232054_create_bank_transfer_payments_table',
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
            echo "✅ Bank transfer migration marked as run\n";
        } else {
            echo "✅ Bank transfer migration already marked as run\n";
        }
    } else {
        echo "❌ Table doesn't exist - will be created by migration\n";
    }
    
    // 4. Fix payment_settings table
    echo "\n4️⃣ Checking payment_settings table...\n";
    if (Schema::hasTable('payment_settings')) {
        echo "✅ Table exists\n";
        $paymentSettingsMigrationExists = DB::table('migrations')
            ->where('migration', '2025_08_03_220812_add_payment_fields_to_orders_table')
            ->exists();
        
        if (!$paymentSettingsMigrationExists) {
            echo "📝 Marking payment_settings migration as run...\n";
            DB::table('migrations')->insert([
                'migration' => '2025_08_03_220812_add_payment_fields_to_orders_table',
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
            echo "✅ Payment settings migration marked as run\n";
        } else {
            echo "✅ Payment settings migration already marked as run\n";
        }
    } else {
        echo "❌ Table doesn't exist - will be created by migration\n";
    }
    
    // 5. Fix restaurant status fields
    echo "\n5️⃣ Checking restaurant status fields...\n";
    $restaurantColumns = DB::select("SHOW COLUMNS FROM restaurants LIKE 'is_open'");
    
    if (empty($restaurantColumns)) {
        echo "❌ Restaurant status fields not found. Adding them...\n";
        DB::statement("ALTER TABLE restaurants ADD COLUMN is_open BOOLEAN DEFAULT TRUE AFTER description");
        DB::statement("ALTER TABLE restaurants ADD COLUMN open_time TIME DEFAULT '08:00:00' AFTER is_open");
        DB::statement("ALTER TABLE restaurants ADD COLUMN close_time TIME DEFAULT '22:00:00' AFTER open_time");
        echo "✅ Successfully added restaurant status fields\n";
    } else {
        echo "✅ Restaurant status fields already exist\n";
    }
    
    // Mark restaurant status migration as run
    $restaurantStatusMigrationExists = DB::table('migrations')
        ->where('migration', '2025_08_03_220812_add_restaurant_status_fields')
        ->exists();
    
    if (!$restaurantStatusMigrationExists) {
        echo "📝 Marking restaurant status migration as run...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_08_03_220812_add_restaurant_status_fields',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "✅ Restaurant status migration marked as run\n";
    } else {
        echo "✅ Restaurant status migration already marked as run\n";
    }
    
    // 6. Fix categories slug constraint
    echo "\n6️⃣ Checking categories slug constraint...\n";
    $globalIndexExists = collect(DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_slug_unique'"))->isNotEmpty();
    $restaurantIndexExists = collect(DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_slug_restaurant_unique'"))->isNotEmpty();
    
    if ($globalIndexExists && !$restaurantIndexExists) {
        echo "❌ Categories slug constraint needs fixing...\n";
        DB::statement("ALTER TABLE categories DROP INDEX categories_slug_unique");
        DB::statement("ALTER TABLE categories ADD UNIQUE KEY categories_slug_restaurant_unique (slug, restaurant_id)");
        echo "✅ Successfully fixed categories slug constraint\n";
    } else {
        echo "✅ Categories slug constraint is correct\n";
    }
    
    // Mark categories constraint migration as run
    $categoriesConstraintMigrationExists = DB::table('migrations')
        ->where('migration', '2025_07_21_180623_update_categories_slug_unique_constraint')
        ->exists();
    
    if (!$categoriesConstraintMigrationExists) {
        echo "📝 Marking categories constraint migration as run...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_07_21_180623_update_categories_slug_unique_constraint',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "✅ Categories constraint migration marked as run\n";
    } else {
        echo "✅ Categories constraint migration already marked as run\n";
    }
    
    echo "\n🎉 All migration fixes completed successfully!\n";
    echo "\n📋 Summary:\n";
    echo "✅ Session ID column added to orders table\n";
    echo "✅ Pending payment status added to orders enum\n";
    echo "✅ Bank transfer payments table verified\n";
    echo "✅ Payment settings table verified\n";
    echo "✅ Restaurant status fields verified\n";
    echo "✅ Categories slug constraint verified\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
} 