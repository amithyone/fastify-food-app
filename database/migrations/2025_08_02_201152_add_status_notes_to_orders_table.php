<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('status_note')->nullable()->after('status');
            $table->timestamp('status_updated_at')->nullable()->after('status_note');
            $table->foreignId('status_updated_by')->nullable()->after('status_updated_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['status_updated_by']);
            $table->dropColumn(['status_note', 'status_updated_at', 'status_updated_by']);
        });
    }
};
