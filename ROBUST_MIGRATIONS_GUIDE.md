# 🛡️ Robust Migrations Guide

## Overview

This guide explains how to create migrations that are robust and handle existing tables/columns gracefully, preventing errors when migrations are run multiple times or on servers with different states.

## 🚀 Key Principles

1. **Check Before Create**: Always check if tables/columns exist before creating them
2. **Safe Updates**: Update existing structures only when necessary
3. **Graceful Rollbacks**: Handle rollbacks safely
4. **Detailed Logging**: Log all actions for debugging

## 📋 Migration Patterns

### 1. Adding Columns Safely

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if column exists before adding
        $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'session_id'");
        
        if (empty($columns)) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('session_id')->nullable()->after('user_id');
                $table->index('session_id');
            });
        } else {
            // Column exists, just ensure index exists
            $indexes = DB::select("SHOW INDEX FROM orders WHERE Key_name = 'idx_session_id'");
            if (empty($indexes)) {
                DB::statement("ALTER TABLE orders ADD INDEX idx_session_id (session_id)");
            }
        }
    }

    public function down(): void
    {
        // Check if column exists before removing
        $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'session_id'");
        
        if (!empty($columns)) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['session_id']);
                $table->dropColumn('session_id');
            });
        }
    }
};
```

### 2. Creating Tables Safely

```php
public function up(): void
{
    if (!Schema::hasTable('new_table')) {
        Schema::create('new_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }
}

public function down(): void
{
    if (Schema::hasTable('new_table')) {
        Schema::dropIfExists('new_table');
    }
}
```

### 3. Modifying Enums Safely

```php
public function up(): void
{
    $result = DB::select("SHOW COLUMNS FROM orders WHERE Field = 'status'");
    if (!empty($result)) {
        $currentType = $result[0]->Type;
        $enumValues = $this->extractEnumValues($currentType);
        
        // Add new enum values if they don't exist
        $newValues = ['pending_payment'];
        $missingValues = array_diff($newValues, $enumValues);
        
        if (!empty($missingValues)) {
            $allValues = array_merge($enumValues, $missingValues);
            $enumString = "ENUM('" . implode("','", $allValues) . "')";
            DB::statement("ALTER TABLE orders MODIFY COLUMN status {$enumString}");
        }
    }
}

private function extractEnumValues(string $type): array
{
    if (preg_match("/^enum\((.*)\)$/", $type, $matches)) {
        $values = str_getcsv($matches[1], ',', "'");
        return array_map('trim', $values);
    }
    return [];
}
```

## 🛠️ Using the RobustMigration Base Class

### 1. Extend the Base Class

```php
<?php

use App\Database\Migrations\RobustMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends RobustMigration
{
    public function up(): void
    {
        // Add column safely
        $this->addColumnIfNotExists('orders', 'session_id', function (Blueprint $table) {
            $table->string('session_id')->nullable()->after('user_id');
            $table->index('session_id');
        });
        
        // Create table safely
        $this->createTableIfNotExists('guest_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->timestamps();
        });
        
        // Modify enum safely
        $this->modifyEnumIfNeeded('orders', 'status', [
            'pending', 'pending_payment', 'confirmed', 'cancelled'
        ]);
    }

    public function down(): void
    {
        $this->removeColumnIfExists('orders', 'session_id');
        $this->dropTableIfExists('guest_sessions');
    }
};
```

### 2. Available Methods

- `addColumnIfNotExists($table, $column, $callback)`
- `removeColumnIfExists($table, $column)`
- `createTableIfNotExists($table, $callback)`
- `dropTableIfExists($table)`
- `addIndexIfNotExists($table, $indexName, $columns)`
- `removeIndexIfExists($table, $indexName)`
- `modifyEnumIfNeeded($table, $column, $values)`
- `addForeignKeyIfNotExists($table, $column, $referencedTable, $referencedColumn)`
- `removeForeignKeyIfExists($table, $column)`

## 🔧 Best Practices

### 1. Always Check Before Acting

```php
// ❌ Bad - Will fail if column exists
Schema::table('orders', function (Blueprint $table) {
    $table->string('session_id');
});

// ✅ Good - Checks first
$columns = DB::select("SHOW COLUMNS FROM orders LIKE 'session_id'");
if (empty($columns)) {
    Schema::table('orders', function (Blueprint $table) {
        $table->string('session_id');
    });
}
```

### 2. Handle Indexes Separately

```php
// Add column
$this->addColumnIfNotExists('orders', 'session_id', function (Blueprint $table) {
    $table->string('session_id')->nullable();
});

// Add index separately
$this->addIndexIfNotExists('orders', 'idx_session_id', ['session_id']);
```

### 3. Log Actions for Debugging

```php
protected function log(string $message): void
{
    echo "Migration: {$message}\n";
}
```

### 4. Use Transactions for Complex Changes

```php
public function up(): void
{
    DB::transaction(function () {
        $this->addColumnIfNotExists('orders', 'session_id', function (Blueprint $table) {
            $table->string('session_id')->nullable();
        });
        
        $this->addIndexIfNotExists('orders', 'idx_session_id', ['session_id']);
    });
}
```

## 🚨 Common Pitfalls

### 1. Not Checking for Existing Columns
```php
// ❌ Will fail if column exists
$table->string('new_column');

// ✅ Safe approach
$columns = DB::select("SHOW COLUMNS FROM table LIKE 'new_column'");
if (empty($columns)) {
    $table->string('new_column');
}
```

### 2. Not Handling Enum Updates
```php
// ❌ Will fail if enum value doesn't exist
DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed')");

// ✅ Safe approach
$this->modifyEnumIfNeeded('orders', 'status', ['pending', 'confirmed']);
```

### 3. Not Checking Indexes
```php
// ❌ Will fail if index exists
$table->index('column_name');

// ✅ Safe approach
$indexes = DB::select("SHOW INDEX FROM table WHERE Key_name = 'index_name'");
if (empty($indexes)) {
    $table->index('column_name');
}
```

## 📝 Migration Template

Use this template for new migrations:

```php
<?php

use App\Database\Migrations\RobustMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends RobustMigration
{
    public function up(): void
    {
        // Add your robust migration logic here
        $this->addColumnIfNotExists('table_name', 'column_name', function (Blueprint $table) {
            $table->string('column_name')->nullable();
        });
    }

    public function down(): void
    {
        // Add your rollback logic here
        $this->removeColumnIfExists('table_name', 'column_name');
    }
};
```

## 🎯 Benefits

1. **No Migration Errors**: Migrations can be run multiple times safely
2. **Production Safe**: Works on servers with different states
3. **Debugging Friendly**: Detailed logging of all actions
4. **Maintainable**: Clear patterns for common operations
5. **Rollback Safe**: Graceful handling of rollbacks

## 🔄 Migration Commands

```bash
# Run migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset

# Fresh start with seeding
php artisan migrate:fresh --seed
```

This robust migration pattern ensures your database migrations are safe, reliable, and can be run multiple times without errors! 🛡️ 