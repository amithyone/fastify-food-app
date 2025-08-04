<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Example: Adding a column to existing table
        $this->addColumnIfNotExists('table_name', 'column_name', function (Blueprint $table) {
            $table->string('column_name')->nullable()->after('existing_column');
            $table->index('column_name');
        });
        
        // Example: Creating a new table if it doesn't exist
        $this->createTableIfNotExists('new_table_name', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        
        // Example: Modifying enum values
        $this->modifyEnumIfNeeded('table_name', 'status_column', [
            'old_value1',
            'old_value2', 
            'new_value1',
            'new_value2'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Example: Removing column if it exists
        $this->removeColumnIfExists('table_name', 'column_name');
        
        // Example: Dropping table if it exists
        $this->dropTableIfExists('new_table_name');
    }
    
    /**
     * Add column if it doesn't exist
     */
    private function addColumnIfNotExists(string $table, string $column, callable $callback): void
    {
        $columns = DB::select("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        
        if (empty($columns)) {
            Schema::table($table, $callback);
        } else {
            // Column exists, you might want to add indexes or modify it
            $this->log("Column {$column} already exists in table {$table}");
        }
    }
    
    /**
     * Remove column if it exists
     */
    private function removeColumnIfExists(string $table, string $column): void
    {
        $columns = DB::select("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        
        if (!empty($columns)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($column) {
                $tableBlueprint->dropColumn($column);
            });
        } else {
            $this->log("Column {$column} doesn't exist in table {$table}");
        }
    }
    
    /**
     * Create table if it doesn't exist
     */
    private function createTableIfNotExists(string $table, callable $callback): void
    {
        if (!Schema::hasTable($table)) {
            Schema::create($table, $callback);
        } else {
            $this->log("Table {$table} already exists");
        }
    }
    
    /**
     * Drop table if it exists
     */
    private function dropTableIfExists(string $table): void
    {
        if (Schema::hasTable($table)) {
            Schema::dropIfExists($table);
        } else {
            $this->log("Table {$table} doesn't exist");
        }
    }
    
    /**
     * Modify enum values if needed
     */
    private function modifyEnumIfNeeded(string $table, string $column, array $values): void
    {
        $result = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'");
        if (!empty($result)) {
            $currentType = $result[0]->Type;
            $enumValues = $this->extractEnumValues($currentType);
            
            // Check if we need to add new values
            $newValues = array_diff($values, $enumValues);
            if (!empty($newValues)) {
                $allValues = array_merge($enumValues, $newValues);
                $enumString = "ENUM('" . implode("','", $allValues) . "')";
                DB::statement("ALTER TABLE {$table} MODIFY COLUMN {$column} {$enumString}");
                $this->log("Updated enum values for {$table}.{$column}");
            }
        }
    }
    
    /**
     * Extract enum values from column type
     */
    private function extractEnumValues(string $type): array
    {
        if (preg_match("/^enum\((.*)\)$/", $type, $matches)) {
            $values = str_getcsv($matches[1], ',', "'");
            return array_map('trim', $values);
        }
        return [];
    }
    
    /**
     * Log migration actions
     */
    private function log(string $message): void
    {
        echo "Migration: {$message}\n";
    }
}; 