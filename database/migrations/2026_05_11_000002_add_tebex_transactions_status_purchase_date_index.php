<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optimises filters used by completed()->inPeriod() (leaderboard aggregates).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tebex_transactions', function (Blueprint $table) {
            $table->index(['status', 'purchase_date'], 'tebex_transactions_status_purchase_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('tebex_transactions', function (Blueprint $table) {
            $table->dropIndex('tebex_transactions_status_purchase_date_index');
        });
    }
};
