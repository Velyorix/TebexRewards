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
}

