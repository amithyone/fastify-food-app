<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Fixing orders status enum on live server...\n";

try {
    // Check current enum values
    $result = DB::select("SHOW COLUMNS FROM orders WHERE Field = 'status'");
    $currentType = $result[0]->Type;
    
    echo "Current status enum: {$currentType}\n";
    
    // Check if pending_payment is already in the enum
    if (strpos($currentType, 'pending_payment') === false) {
        echo "❌ 'pending_payment' not found in enum. Adding it...\n";
        
        // Add pending_payment to the enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'pending_payment', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending'");
        
        echo "✅ Successfully added 'pending_payment' to status enum\n";
    } else {
        echo "✅ 'pending_payment' already exists in enum\n";
    }
    
    // Verify the change
    $result = DB::select("SHOW COLUMNS FROM orders WHERE Field = 'status'");
    $newType = $result[0]->Type;
    echo "Updated status enum: {$newType}\n";
    
    // Mark the migration as run
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
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "🎉 Orders status enum fix completed successfully!\n"; 