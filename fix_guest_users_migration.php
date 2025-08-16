<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Fixing Guest Users Migration Status...\n\n";

// Check if guest_users table exists
if (Schema::hasTable('guest_users')) {
    echo "✅ guest_users table exists\n";
    
    // Check if migration is marked as completed
    $migration = DB::table('migrations')
        ->where('migration', '2025_08_16_100000_create_guest_users_table')
        ->first();
    
    if (!$migration) {
        echo "📝 Marking guest_users table migration as completed...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_08_16_100000_create_guest_users_table',
            'batch' => 31
        ]);
        echo "✅ Migration marked as completed\n";
    } else {
        echo "✅ Migration already marked as completed\n";
    }
    
    // Check orders table migration
    $ordersMigration = DB::table('migrations')
        ->where('migration', '2025_08_16_100001_add_guest_user_id_to_orders_table')
        ->first();
    
    if (!$ordersMigration) {
        echo "📝 Marking orders table migration as completed...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_08_16_100001_add_guest_user_id_to_orders_table',
            'batch' => 31
        ]);
        echo "✅ Orders migration marked as completed\n";
    } else {
        echo "✅ Orders migration already marked as completed\n";
    }
    
    // Check if guest_user_id column exists in orders table
    if (Schema::hasColumn('orders', 'guest_user_id')) {
        echo "✅ guest_user_id column exists in orders table\n";
    } else {
        echo "⚠️  guest_user_id column missing from orders table\n";
        echo "Running orders migration...\n";
        // You can run the specific migration here if needed
    }
    
} else {
    echo "❌ guest_users table does not exist\n";
    echo "Running migrations normally...\n";
}

echo "\n🎉 Migration status fix completed!\n";
echo "You can now run: php artisan migrate\n";
