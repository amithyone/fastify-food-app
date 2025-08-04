<?php

use App\Database\Migrations\RobustMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends RobustMigration
{
    public function up(): void
    {
        // Add role field to users table
        $this->addColumnIfNotExists('users', 'role', function (Blueprint $table) {
            $table->enum('role', ['user', 'manager', 'admin'])->default('user')->after('email');
        });

        // Add manager-specific fields to users table
        $this->addColumnIfNotExists('users', 'business_name', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('role');
        });

        $this->addColumnIfNotExists('users', 'business_registration_number', function (Blueprint $table) {
            $table->string('business_registration_number')->nullable()->after('business_name');
        });

        $this->addColumnIfNotExists('users', 'cac_number', function (Blueprint $table) {
            $table->string('cac_number')->nullable()->after('business_registration_number');
        });

        $this->addColumnIfNotExists('users', 'business_address', function (Blueprint $table) {
            $table->text('business_address')->nullable()->after('cac_number');
        });

        $this->addColumnIfNotExists('users', 'business_phone', function (Blueprint $table) {
            $table->string('business_phone')->nullable()->after('business_address');
        });

        $this->addColumnIfNotExists('users', 'manager_verification_status', function (Blueprint $table) {
            $table->enum('manager_verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('business_phone');
        });

        $this->addColumnIfNotExists('users', 'manager_verification_notes', function (Blueprint $table) {
            $table->text('manager_verification_notes')->nullable()->after('manager_verification_status');
        });

        // Add indexes for better performance
        $this->addIndexIfNotExists('users', 'idx_users_role', ['role']);
        $this->addIndexIfNotExists('users', 'idx_users_manager_status', ['manager_verification_status']);
    }

    public function down(): void
    {
        $this->removeColumnIfExists('users', 'manager_verification_notes');
        $this->removeColumnIfExists('users', 'manager_verification_status');
        $this->removeColumnIfExists('users', 'business_phone');
        $this->removeColumnIfExists('users', 'business_address');
        $this->removeColumnIfExists('users', 'cac_number');
        $this->removeColumnIfExists('users', 'business_registration_number');
        $this->removeColumnIfExists('users', 'business_name');
        $this->removeColumnIfExists('users', 'role');
    }
}; 