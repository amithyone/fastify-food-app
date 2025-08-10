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
        Schema::table('video_packages', function (Blueprint $table) {
            $table->string('dishes_to_film')->nullable()->after('special_instructions');
            $table->string('staff_contact')->nullable()->after('dishes_to_film');
            $table->text('filming_requirements')->nullable()->after('staff_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_packages', function (Blueprint $table) {
            $table->dropColumn(['dishes_to_film', 'staff_contact', 'filming_requirements']);
        });
    }
};
