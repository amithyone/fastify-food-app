<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Fixing all migration issues on live server...\n\n";

try {
    // 1. Fix bank_transfer_payments table
    echo "1️⃣ Checking bank_transfer_payments table...\n";
    if (Schema::hasTable('bank_transfer_payments')) {
        echo "✅ Table exists\n";
        
        $migrationExists = DB::table('migrations')
            ->where('migration', '2025_08_03_232054_create_bank_transfer_payments_table')
            ->exists();
        
        if (!$migrationExists) {
            echo "📝 Marking migration as run...\n";
            DB::table('migrations')->insert([
                'migration' => '2025_08_03_232054_create_bank_transfer_payments_table',
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
            echo "✅ Migration marked as run\n";
        } else {
            echo "✅ Migration already marked as run\n";
        }
    } else {
        echo "❌ Table doesn't exist - will be created by migration\n";
    }
    
    // 2. Fix payvibe fields migration
    echo "\n2️⃣ Checking payvibe fields migration...\n";
    if (Schema::hasTable('bank_transfer_payments')) {
        $hasNetAmount = Schema::hasColumn('bank_transfer_payments', 'net_amount');
        
        if (!$hasNetAmount) {
            echo "❌ PayVibe fields not added yet\n";
        } else {
            echo "✅ PayVibe fields already exist\n";
            
            $migrationExists = DB::table('migrations')
                ->where('migration', '2025_08_03_233102_add_payvibe_fields_to_bank_transfer_payments_table')
                ->exists();
            
            if (!$migrationExists) {
                echo "📝 Marking migration as run...\n";
                DB::table('migrations')->insert([
                    'migration' => '2025_08_03_233102_add_payvibe_fields_to_bank_transfer_payments_table',
                    'batch' => DB::table('migrations')->max('batch') + 1
                ]);
                echo "✅ Migration marked as run\n";
            } else {
                echo "✅ Migration already marked as run\n";
            }
        }
    }
    
    // 3. Fix restaurant status fields
    echo "\n3️⃣ Checking restaurant status fields...\n";
    if (Schema::hasTable('restaurants')) {
        $hasIsOpen = Schema::hasColumn('restaurants', 'is_open');
        
        if (!$hasIsOpen) {
            echo "❌ Restaurant status fields not added yet\n";
        } else {
            echo "✅ Restaurant status fields already exist\n";
            
            $migrationExists = DB::table('migrations')
                ->where('migration', '2025_08_03_234537_add_open_close_status_to_restaurants_table')
                ->exists();
            
            if (!$migrationExists) {
                echo "📝 Marking migration as run...\n";
                DB::table('migrations')->insert([
                    'migration' => '2025_08_03_234537_add_open_close_status_to_restaurants_table',
                    'batch' => DB::table('migrations')->max('batch') + 1
                ]);
                echo "✅ Migration marked as run\n";
            } else {
                echo "✅ Migration already marked as run\n";
            }
        }
    }
    
    // 4. Fix orders status enum
    echo "\n4️⃣ Checking orders status enum...\n";
    $result = DB::select("SHOW COLUMNS FROM orders WHERE Field = 'status'");
    $currentType = $result[0]->Type;
    
    if (strpos($currentType, 'pending_payment') === false) {
        echo "❌ 'pending_payment' not in enum. Adding it...\n";
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'pending_payment', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending'");
        echo "✅ Successfully added 'pending_payment' to status enum\n";
    } else {
        echo "✅ 'pending_payment' already exists in enum\n";
    }
    
    $migrationExists = DB::table('migrations')
        ->where('migration', '2025_08_04_000644_add_pending_payment_to_orders_status_enum')
        ->exists();
    
    if (!$migrationExists) {
        echo "📝 Marking migration as run...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_08_04_000644_add_pending_payment_to_orders_status_enum',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "✅ Migration marked as run\n";
    } else {
        echo "✅ Migration already marked as run\n";
    }
    
    // 5. Fix categories slug constraint
    echo "\n5️⃣ Checking categories slug constraint...\n";
    $indexes = DB::select("SHOW INDEX FROM categories");
    $hasRestaurantIndex = false;
    $hasGlobalIndex = false;
    
    foreach ($indexes as $index) {
        if ($index->Key_name === 'categories_slug_restaurant_unique') {
            $hasRestaurantIndex = true;
        }
        if ($index->Key_name === 'categories_slug_unique') {
            $hasGlobalIndex = true;
        }
    }
    
    if ($hasRestaurantIndex && !$hasGlobalIndex) {
        echo "✅ Categories slug constraint is correct\n";
        
        $migrationExists = DB::table('migrations')
            ->where('migration', '2025_07_21_180623_update_categories_slug_unique_constraint')
            ->exists();
        
        if (!$migrationExists) {
            echo "📝 Marking migration as run...\n";
            DB::table('migrations')->insert([
                'migration' => '2025_07_21_180623_update_categories_slug_unique_constraint',
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
            echo "✅ Migration marked as run\n";
        } else {
            echo "✅ Migration already marked as run\n";
        }
    } else {
        echo "⚠️ Categories slug constraint needs attention\n";
    }
    
    echo "\n🎉 All migration fixes completed!\n";
    echo "📊 Summary:\n";
    echo "- Bank transfer payments table: " . (Schema::hasTable('bank_transfer_payments') ? "✅" : "❌") . "\n";
    echo "- Restaurant status fields: " . (Schema::hasColumn('restaurants', 'is_open') ? "✅" : "❌") . "\n";
    echo "- Orders status enum: " . (strpos($currentType, 'pending_payment') !== false ? "✅" : "❌") . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
} 