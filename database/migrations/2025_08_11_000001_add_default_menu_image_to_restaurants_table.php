<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('restaurants') && !Schema::hasColumn('restaurants', 'default_menu_image')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->string('default_menu_image')->nullable()->after('banner_image');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('restaurants') && Schema::hasColumn('restaurants', 'default_menu_image')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropColumn('default_menu_image');
            });
        }
    }
};


