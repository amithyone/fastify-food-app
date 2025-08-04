<?php

namespace App\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

abstract class RobustMigration extends Migration
{
    /**
     * Add column if it doesn't exist
     */
    protected function addColumnIfNotExists(string $table, string $column, callable $callback): void
    {
        $columns = DB::select("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        
        if (empty($columns)) {
            Schema::table($table, $callback);
            $this->log("Added column {$column} to table {$table}");
        } else {
            $this->log("Column {$column} already exists in table {$table}");
        }
    }
    
    /**
     * Remove column if it exists
     */
    protected function removeColumnIfExists(string $table, string $column): void
    {
        $columns = DB::select("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        
        if (!empty($columns)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($column) {
                $tableBlueprint->dropColumn($column);
            });
            $this->log("Removed column {$column} from table {$table}");
        } else {
            $this->log("Column {$column} doesn't exist in table {$table}");
        }
    }
    
    /**
     * Create table if it doesn't exist
     */
    protected function createTableIfNotExists(string $table, callable $callback): void
    {
        if (!Schema::hasTable($table)) {
            Schema::create($table, $callback);
            $this->log("Created table {$table}");
        } else {
            $this->log("Table {$table} already exists");
        }
    }
    
    /**
     * Drop table if it exists
     */
    protected function dropTableIfExists(string $table): void
    {
        if (Schema::hasTable($table)) {
            Schema::dropIfExists($table);
            $this->log("Dropped table {$table}");
        } else {
            $this->log("Table {$table} doesn't exist");
        }
    }
    
    /**
     * Add index if it doesn't exist
     */
    protected function addIndexIfNotExists(string $table, string $indexName, array $columns): void
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        
        if (empty($indexes)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName, $columns) {
                $tableBlueprint->index($columns, $indexName);
            });
            $this->log("Added index {$indexName} to table {$table}");
        } else {
            $this->log("Index {$indexName} already exists in table {$table}");
        }
    }
    
    /**
     * Remove index if it exists
     */
    protected function removeIndexIfExists(string $table, string $indexName): void
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        
        if (!empty($indexes)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName) {
                $tableBlueprint->dropIndex($indexName);
            });
            $this->log("Removed index {$indexName} from table {$table}");
        } else {
            $this->log("Index {$indexName} doesn't exist in table {$table}");
        }
    }
    
    /**
     * Modify enum values if needed
     */
    protected function modifyEnumIfNeeded(string $table, string $column, array $values): void
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
                $this->log("Updated enum values for {$table}.{$column}: " . implode(', ', $newValues));
            } else {
                $this->log("Enum values for {$table}.{$column} are already up to date");
            }
        }
    }
    
    /**
     * Add foreign key if it doesn't exist
     */
    protected function addForeignKeyIfNotExists(string $table, string $column, string $referencedTable, string $referencedColumn = 'id'): void
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '{$table}' 
            AND COLUMN_NAME = '{$column}' 
            AND REFERENCED_TABLE_NAME = '{$referencedTable}'
        ");
        
        if (empty($foreignKeys)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($column, $referencedTable, $referencedColumn) {
                $tableBlueprint->foreign($column)->references($referencedColumn)->on($referencedTable);
            });
            $this->log("Added foreign key {$column} -> {$referencedTable}.{$referencedColumn}");
        } else {
            $this->log("Foreign key {$column} -> {$referencedTable}.{$referencedColumn} already exists");
        }
    }
    
    /**
     * Remove foreign key if it exists
     */
    protected function removeForeignKeyIfExists(string $table, string $column): void
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '{$table}' 
            AND COLUMN_NAME = '{$column}' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (!empty($foreignKeys)) {
            $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
            Schema::table($table, function (Blueprint $tableBlueprint) use ($constraintName) {
                $tableBlueprint->dropForeign($constraintName);
            });
            $this->log("Removed foreign key {$constraintName} from table {$table}");
        } else {
            $this->log("No foreign key found for column {$column} in table {$table}");
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
    protected function log(string $message): void
    {
        echo "Migration: {$message}\n";
    }
} 