<?php

namespace Azuriom\Plugin\Tebexrewards\Support;

use Azuriom\Plugin\Tebexrewards\Models\RankTier;
use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Azuriom\Plugin\Tebexrewards\Services\GoalProgressService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class ShopViewData
{
    /**
     * Données partagées pour le bandeau boutique (thème KrustyMC).
     *
     * @return array{
     *     tebexRewardsEnabled: bool,
     *     shopGoal: array,
     *     shopTopMonthly: array|null,
     *     shopTop3Monthly: array,
     *     shopRecentBuyers: \Illuminate\Support\Collection
     * }
     */
    public static function get(): array
    {
        $tebexRewardsEnabled = plugins()->isEnabled('tebexrewards');
        $shopGoal = [];
        $shopTopMonthly = null;
        $shopTop3Monthly = [];
        /** @var Collection<int, object> $shopRecentBuyers */
        $shopRecentBuyers = collect();

        if (! $tebexRewardsEnabled) {
            return compact(
                'tebexRewardsEnabled',
                'shopGoal',
                'shopTopMonthly',
                'shopTop3Monthly',
                'shopRecentBuyers'
            );
        }

        $shopGoal = Cache::remember(
            TebexRewardsCache::GOAL_WIDGET_KEY,
            TebexRewardsCache::TTL_GOAL_WIDGET_SECONDS,
            fn () => app(GoalProgressService::class)->get()
        );

        $shopTopMonthly = Cache::remember(
            TebexRewardsCache::leaderboardPageKey('month', 1),
            TebexRewardsCache::TTL_LEADERBOARD_PAGE_SECONDS,
            function () {
                $row = Transaction::leaderboard(1, 'month')->first();

                return $row ? self::enrichLeaderboardEntry($row) : null;
            }
        );

        $shopTop3Monthly = Cache::remember(
            'tebexrewards.shop.top3_month.v2',
            TebexRewardsCache::TTL_LEADERBOARD_PAGE_SECONDS,
            fn () => Transaction::leaderboard(3, 'month')
                ->map(fn (array $entry) => self::enrichLeaderboardEntry($entry))
                ->values()
                ->all()
        );

        $shopRecentBuyers = Cache::remember(
            'tebexrewards.shop.recent_buyers.v1',
            45,
            function () {
                $rows = Transaction::query()
                    ->completed()
                    ->orderByDesc('purchase_date')
                    ->limit(40)
                    ->get(['player_name', 'player_uuid']);

                return $rows->unique('player_name')->take(8);
            }
        );

        return compact(
            'tebexRewardsEnabled',
            'shopGoal',
            'shopTopMonthly',
            'shopTop3Monthly',
            'shopRecentBuyers'
        );
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return array<string, mixed>
     */
    public static function enrichLeaderboardEntry(array $entry): array
    {
        $lifetimeTotal = (float) Transaction::query()
            ->completed()
            ->where('player_name', $entry['player_name'] ?? '')
            ->sum('amount');

        $tier = RankTier::bestForAmount($lifetimeTotal);
        if ($tier !== null) {
            $entry['supporter_tier'] = [
                'name' => $tier->rank_name,
                'icon' => $tier->icon,
                'color' => $tier->color_hex,
            ];
        }

        return $entry;
    }
}
