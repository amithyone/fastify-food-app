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
        Schema::table('bank_transfer_payments', function (Blueprint $table) {
            $table->decimal('payvibe_net_amount', 10, 2)->nullable()->after('notes');
            $table->decimal('payvibe_bank_charge', 10, 2)->nullable()->after('payvibe_net_amount');
            $table->decimal('payvibe_platform_fee', 10, 2)->nullable()->after('payvibe_bank_charge');
            $table->decimal('payvibe_settled_amount', 10, 2)->nullable()->after('payvibe_platform_fee');
            $table->decimal('payvibe_platform_profit', 10, 2)->nullable()->after('payvibe_settled_amount');
            $table->decimal('payvibe_transaction_amount', 10, 2)->nullable()->after('payvibe_platform_profit');
            $table->datetime('payvibe_credited_at')->nullable()->after('payvibe_transaction_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_transfer_payments', function (Blueprint $table) {
            $table->dropColumn([
                'payvibe_net_amount',
                'payvibe_bank_charge',
                'payvibe_platform_fee',
                'payvibe_settled_amount',
                'payvibe_platform_profit',
                'payvibe_transaction_amount',
                'payvibe_credited_at'
            ]);
        });
    }
};
