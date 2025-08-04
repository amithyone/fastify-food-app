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
        // Check if session_id column already exists
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if session_id column exists before trying to remove it
        $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'session_id'");
        
        if (!empty($columns)) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['session_id']);
                $table->dropColumn('session_id');
            });
        }
    }
};
