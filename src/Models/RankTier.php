<?php

namespace Azuriom\Plugin\Tebexrewards\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $rank_name
 * @property string $min_amount
 * @property string|null $icon
 * @property string|null $color_hex
 * @property bool $enabled
 */
class RankTier extends Model
{
    protected $table = 'tebex_rank_tiers';

    protected $fillable = [
        'rank_name',
        'min_amount',
        'icon',
        'color_hex',
        'enabled',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'enabled' => 'boolean',
    ];

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public static function bestForAmount(float $amount): ?self
    {
        return static::query()
            ->enabled()
            ->where('min_amount', '<=', $amount)
            ->orderByDesc('min_amount')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @return list<array{rank_name:string,min_amount:string,icon:?string,color_hex:?string,enabled:bool}>
     */
    public static function defaultTierRows(): array
    {
        return [
            ['rank_name' => 'Soutien Bronze', 'min_amount' => '10.00', 'icon' => '🟤', 'color_hex' => '#8B4513', 'enabled' => true],
            ['rank_name' => 'Soutien Argent', 'min_amount' => '50.00', 'icon' => '🥈', 'color_hex' => '#c0c0c0', 'enabled' => true],
            ['rank_name' => 'Soutien Or', 'min_amount' => '100.00', 'icon' => '🥇', 'color_hex' => '#ffd700', 'enabled' => true],
            ['rank_name' => 'Soutien Platine', 'min_amount' => '250.00', 'icon' => '🔵', 'color_hex' => '#4169E1', 'enabled' => true],
            ['rank_name' => 'Soutien Diamant', 'min_amount' => '500.00', 'icon' => '💎', 'color_hex' => '#00CED1', 'enabled' => true],
            ['rank_name' => 'Soutien Émeraude', 'min_amount' => '1000.00', 'icon' => '🟢', 'color_hex' => '#2E8B57', 'enabled' => true],
            ['rank_name' => 'Soutien Légendaire', 'min_amount' => '5000.00', 'icon' => '🔴', 'color_hex' => '#DC143C', 'enabled' => true],
        ];
    }
}

