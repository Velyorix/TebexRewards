<?php

namespace Azuriom\Plugin\Tebexrewards\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $player_uuid
 * @property string $player_name
 * @property int|null $package_id
 * @property string|null $package_name
 * @property string $amount
 * @property string|null $currency
 * @property \Illuminate\Support\Carbon $purchase_date
 * @property string $status
 * @property string|null $tebex_transaction_id
 * @property array|null $raw_payload
 */
class Transaction extends Model
{
    protected $table = 'tebex_transactions';

    protected $fillable = [
        'player_uuid',
        'player_name',
        'package_id',
        'package_name',
        'amount',
        'currency',
        'purchase_date',
        'status',
        'tebex_transaction_id',
        'raw_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'purchase_date' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'complete');
    }

    public function scopeInPeriod(Builder $query, string $period, ?\DateTimeInterface $now = null): Builder
    {
        if ($period === 'all') {
            return $query;
        }

        $now = $now ? CarbonImmutable::instance($now) : CarbonImmutable::now();

        $start = match ($period) {
            'day' => $now->startOfDay(),
            'week' => $now->startOfWeek(),
            'month' => $now->startOfMonth(),
            default => null,
        };

        if ($start === null) {
            return $query;
        }

        return $query->where('purchase_date', '>=', $start);
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{rank:int, player_name:string, player_uuid:?string, total_amount:string, purchases_count:int}>
     */
    public static function leaderboard(int $limit, string $period): \Illuminate\Support\Collection
    {
        $rows = static::query()
            ->completed()
            ->inPeriod($period)
            ->selectRaw('player_uuid, player_name, SUM(amount) as total_amount, COUNT(*) as purchases_count')
            ->groupBy('player_uuid', 'player_name')
            ->orderByDesc('total_amount')
            ->orderByDesc('purchases_count')
            ->limit($limit)
            ->get();

        return $rows->values()->map(function ($row, int $index) {
            return [
                'rank' => $index + 1,
                'player_name' => (string) $row->player_name,
                'player_uuid' => $row->player_uuid ? (string) $row->player_uuid : null,
                'total_amount' => (string) $row->total_amount,
                'purchases_count' => (int) $row->purchases_count,
            ];
        });
    }

    public static function totalDonationsAllTime(): string
    {
        $sum = (string) (static::query()->completed()->sum('amount') ?? '0');

        return number_format((float) $sum, 2, '.', '');
    }

    /**
     * @return array{total_amount: float, transactions_count: int, unique_donors: int}
     */
    public static function donorStats(string $period): array
    {
        $base = static::query()->completed()->inPeriod($period);

        $totalAmount = (float) ((clone $base)->sum('amount') ?? 0);
        $transactionsCount = (int) (clone $base)->count();
        $uniqueDonors = (int) (clone $base)->selectRaw('COUNT(DISTINCT player_name) as aggregate')->value('aggregate');

        return [
            'total_amount' => $totalAmount,
            'transactions_count' => $transactionsCount,
            'unique_donors' => $uniqueDonors,
        ];
    }

    public static function latestCompleted(): ?self
    {
        return static::query()
            ->completed()
            ->select([
                'id',
                'player_name',
                'amount',
                'currency',
                'package_name',
                'purchase_date',
            ])
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->first();
    }
}

