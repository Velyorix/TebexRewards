<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        if (! Schema::hasTable('tebex_rank_tiers')) {
            return;
        }

        if (DB::table('tebex_rank_tiers')->exists()) {
            return;
        }

        $now = now();

        DB::table('tebex_rank_tiers')->insert([
            ['rank_name' => 'Soutien Bronze', 'min_amount' => '10.00', 'icon' => '🟤', 'color_hex' => '#8B4513', 'enabled' => true, 'created_at' => $now, 'updated_at' => $now],
            ['rank_name' => 'Soutien Argent', 'min_amount' => '50.00', 'icon' => '🥈', 'color_hex' => '#c0c0c0', 'enabled' => true, 'created_at' => $now, 'updated_at' => $now],
            ['rank_name' => 'Soutien Or', 'min_amount' => '100.00', 'icon' => '🥇', 'color_hex' => '#ffd700', 'enabled' => true, 'created_at' => $now, 'updated_at' => $now],
            ['rank_name' => 'Soutien Platine', 'min_amount' => '250.00', 'icon' => '🔵', 'color_hex' => '#4169E1', 'enabled' => true, 'created_at' => $now, 'updated_at' => $now],
            ['rank_name' => 'Soutien Diamant', 'min_amount' => '500.00', 'icon' => '💎', 'color_hex' => '#00CED1', 'enabled' => true, 'created_at' => $now, 'updated_at' => $now],
            ['rank_name' => 'Soutien Émeraude', 'min_amount' => '1000.00', 'icon' => '🟢', 'color_hex' => '#2E8B57', 'enabled' => true, 'created_at' => $now, 'updated_at' => $now],
            ['rank_name' => 'Soutien Légendaire', 'min_amount' => '5000.00', 'icon' => '🔴', 'color_hex' => '#DC143C', 'enabled' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
    }
};
