<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Fixing session_id column on live server...\n\n";

try {
    // Check if session_id column exists
    $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'session_id'");
    
    if (empty($columns)) {
        echo "❌ session_id column not found. Adding it...\n";
        
        // Add session_id column
        DB::statement("ALTER TABLE orders ADD COLUMN session_id VARCHAR(255) NULL AFTER user_id");
        
        // Add index for performance
        DB::statement("ALTER TABLE orders ADD INDEX idx_session_id (session_id)");
        
        echo "✅ Successfully added session_id column and index\n";
    } else {
        echo "✅ session_id column already exists\n";
    }
    
    // Mark migration as run
    $migrationExists = DB::table('migrations')
        ->where('migration', '2025_08_04_103905_add_session_id_to_orders_table')
        ->exists();
    
    if (!$migrationExists) {
        echo "📝 Marking migration as run...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_08_04_103905_add_session_id_to_orders_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "✅ Migration marked as run\n";
    } else {
        echo "✅ Migration already marked as run\n";
    }
    
    // Verify the column was added
    $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'session_id'");
    if (!empty($columns)) {
        echo "✅ Verification: session_id column exists\n";
        echo "Column details: " . json_encode($columns[0]) . "\n";
    } else {
        echo "❌ Verification failed: session_id column not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Session ID migration fix completed successfully!\n"; 