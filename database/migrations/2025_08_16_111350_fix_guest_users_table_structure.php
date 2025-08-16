<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_users', function (Blueprint $table) {
            // Check and drop composite indexes if they exist
            $indexes = $this->getIndexes('guest_users');
            
            if (in_array('guest_users_email_is_active_index', $indexes)) {
                $table->dropIndex(['email', 'is_active']);
            }
            
            if (in_array('guest_users_session_token_session_expires_at_index', $indexes)) {
                $table->dropIndex(['session_token', 'session_expires_at']);
            }
            
            // Modify email column to 191 characters (only if needed)
            $columns = $this->getColumns('guest_users');
            if (isset($columns['email']) && $columns['email'] !== 'varchar(191)') {
                $table->string('email', 191)->change();
            }
            
            // Modify session_token column to 191 characters (only if needed)
            if (isset($columns['session_token']) && $columns['session_token'] !== 'varchar(191)') {
                $table->string('session_token', 191)->change();
            }
            
            // Add single column indexes only if they don't exist
            if (!in_array('guest_users_email_index', $indexes)) {
                $table->index('email');
            }
            
            if (!in_array('guest_users_is_active_index', $indexes)) {
                $table->index('is_active');
            }
            
            if (!in_array('guest_users_session_token_index', $indexes)) {
                $table->index('session_token');
            }
            
            if (!in_array('guest_users_session_expires_at_index', $indexes)) {
                $table->index('session_expires_at');
            }
        });
    }
    
    private function getIndexes($tableName)
    {
        $indexes = [];
        $results = DB::select("SHOW INDEX FROM {$tableName}");
        
        foreach ($results as $result) {
            $indexes[] = $result->Key_name;
        }
        
        return array_unique($indexes);
    }
    
    private function getColumns($tableName)
    {
        $columns = [];
        $results = DB::select("DESCRIBE {$tableName}");
        
        foreach ($results as $result) {
            $columns[$result->Field] = $result->Type;
        }
        
        return $columns;
    }

    public function down(): void
    {
        Schema::table('guest_users', function (Blueprint $table) {
            // Check and drop single column indexes if they exist
            $indexes = $this->getIndexes('guest_users');
            
            if (in_array('guest_users_email_index', $indexes)) {
                $table->dropIndex(['email']);
            }
            
            if (in_array('guest_users_is_active_index', $indexes)) {
                $table->dropIndex(['is_active']);
            }
            
            if (in_array('guest_users_session_token_index', $indexes)) {
                $table->dropIndex(['session_token']);
            }
            
            if (in_array('guest_users_session_expires_at_index', $indexes)) {
                $table->dropIndex(['session_expires_at']);
            }
            
            // Revert column lengths (only if needed)
            $columns = $this->getColumns('guest_users');
            if (isset($columns['email']) && $columns['email'] === 'varchar(191)') {
                $table->string('email', 255)->change();
            }
            
            if (isset($columns['session_token']) && $columns['session_token'] === 'varchar(191)') {
                $table->string('session_token', 255)->change();
            }
            
            // Re-add composite indexes only if they don't exist
            if (!in_array('guest_users_email_is_active_index', $indexes)) {
                $table->index(['email', 'is_active']);
            }
            
            if (!in_array('guest_users_session_token_session_expires_at_index', $indexes)) {
                $table->index(['session_token', 'session_expires_at']);
            }
        });
    }
};
