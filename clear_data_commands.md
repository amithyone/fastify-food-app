# 🧹 Data Clearing Commands

## Option 1: Fresh Migration (Recommended)
This will drop all tables and recreate them with fresh data:

```bash
php artisan migrate:fresh --seed
```

## Option 2: Reset and Migrate
This will rollback all migrations and run them again:

```bash
php artisan migrate:reset
php artisan migrate --seed
```

## Option 3: Clear Specific Data Only
Use the custom script to clear only restaurant and user data:

```bash
php clear_all_data.php
```

## Option 4: Manual Clearing
Clear specific tables manually:

```bash
# Clear orders and payments
php artisan tinker --execute="DB::table('order_items')->truncate(); DB::table('orders')->truncate(); DB::table('bank_transfer_payments')->truncate();"

# Clear restaurants and menus
php artisan tinker --execute="DB::table('menu_items')->truncate(); DB::table('categories')->truncate(); DB::table('restaurants')->truncate();"

# Clear users (except admin)
php artisan tinker --execute="DB::table('users')->where('is_admin', false)->delete();"
```

## Option 5: Database Reset
Complete database reset (⚠️ Destructive):

```bash
# Drop all tables and recreate
php artisan migrate:fresh --seed

# Or reset and migrate
php artisan migrate:reset && php artisan migrate --seed
```

## After Clearing Data
After clearing data, run these to restore default settings:

```bash
# Seed payment settings
php artisan db:seed --class=PaymentSettingsSeeder

# Seed sample restaurant (if you have one)
php artisan db:seed --class=SampleRestaurantSeeder

# Seed all data
php artisan db:seed
```

## ⚠️ Important Notes

1. **Backup first** if you have important data
2. **Admin users are preserved** in the custom script
3. **Payment settings will be reset** to defaults
4. **All orders and payments will be lost**
5. **All restaurants and menus will be cleared**

## 🚀 Quick Start After Clearing

```bash
# 1. Clear all data
php clear_all_data.php

# 2. Seed default settings
php artisan db:seed --class=PaymentSettingsSeeder

# 3. Create a test restaurant
php artisan db:seed --class=SampleRestaurantSeeder

# 4. Clear caches
php artisan optimize:clear
``` 