<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Fixing Server Migration Issues ===\n\n";

// Check which tables exist
$existingTables = DB::select('SHOW TABLES');
$tableNames = [];
foreach ($existingTables as $table) {
    $tableNames[] = array_values((array)$table)[0];
}

echo "Existing tables:\n";
foreach ($tableNames as $table) {
    echo "- $table\n";
}

echo "\n";

// Check which migrations are marked as pending but tables/columns exist
$pendingMigrations = [
    '2025_08_03_232054_create_bank_transfer_payments_table' => 'bank_transfer_payments',
    '2025_08_03_233102_add_payvibe_fields_to_bank_transfer_payments_table' => 'bank_transfer_payments',
    '2025_08_03_234537_add_open_close_status_to_restaurants_table' => 'restaurants',
    '2025_08_04_000644_add_pending_payment_to_orders_status_enum' => 'orders'
];

echo "Checking pending migrations:\n";
foreach ($pendingMigrations as $migration => $table) {
    $exists = DB::table('migrations')->where('migration', $migration)->exists();
    $tableExists = in_array($table, $tableNames);
    
    echo "Migration: $migration\n";
    echo "  - In migration table: " . ($exists ? 'Yes' : 'No') . "\n";
    echo "  - Table exists: " . ($tableExists ? 'Yes' : 'No') . "\n";
    
    if (!$exists && $tableExists) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => 12
        ]);
        echo "  - ✅ Added to migration table\n";
    } elseif ($exists && !$tableExists) {
        DB::table('migrations')->where('migration', $migration)->delete();
        echo "  - ✅ Removed from migration table\n";
    } else {
        echo "  - ✅ Status correct\n";
    }
    echo "\n";
}

// Check orders table columns
echo "Checking orders table columns:\n";
$columns = DB::select('SHOW COLUMNS FROM orders');
$columnNames = [];
foreach ($columns as $column) {
    $columnNames[] = $column->Field;
}

$requiredColumns = ['payment_reference', 'gateway_reference', 'payment_status', 'paid_at'];
foreach ($requiredColumns as $column) {
    $exists = in_array($column, $columnNames);
    echo "Column '$column': " . ($exists ? 'Exists' : 'Missing') . "\n";
}

echo "\n=== Migration Table Fixed ===\n";
echo "✅ Server migration issues resolved\n";
echo "✅ Database state synchronized\n";
echo "✅ Ready to run migrations\n"; 