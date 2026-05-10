<?php

namespace Azuriom\Plugin\Tebexrewards\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Centralised cache keys and TTLs (CDC §5 — performance). Invalidated on webhook writes and admin “clear cache”.
 */
final class TebexRewardsCache
{
    public const LAST_PURCHASER_KEY = 'tebexrewards.widgets.last_purchaser';

    public const GOAL_WIDGET_KEY = 'tebexrewards.widgets.goal';

    /** Full HTML leaderboard page fragment (Invalidated on new transactions). */
    public const TTL_LEADERBOARD_PAGE_SECONDS = 600;

    /** Goal bar payload on public page. */
    public const TTL_GOAL_WIDGET_SECONDS = 60;

    public const TTL_LAST_PURCHASER_SECONDS = 30;

    /** JSON widget polled by the leaderboard page — short TTL + same invalidation as webhooks. */
    public const TTL_LEADERBOARD_WIDGET_JSON_SECONDS = 15;

    public static function leaderboardPageKey(string $period, int $limit): string
    {
        return "tebexrewards.leaderboard.{$period}.{$limit}";
    }

    public static function leaderboardWidgetJsonKey(string $period, int $limit): string
    {
        return "tebexrewards.widgets.leaderboard_json.{$period}.{$limit}";
    }

    /**
     * Admin-configured limits (must match TebexRewardsSettingsRequest).
     *
     * @return list<int>
     */
    public static function leaderboardLimitValues(): array
    {
        return [5, 10, 25, 50, 100];
    }

    /**
     * Forget all derived caches (leaderboard HTML + widget JSON for every limit/period, goal, last purchaser).
     */
    public static function invalidateAll(): void
    {
        foreach (self::leaderboardLimitValues() as $limit) {
            foreach (['all', 'month', 'week', 'day'] as $period) {
                Cache::forget(self::leaderboardPageKey($period, $limit));
                Cache::forget(self::leaderboardWidgetJsonKey($period, $limit));
            }
        }

        Cache::forget(self::LAST_PURCHASER_KEY);
        Cache::forget(self::GOAL_WIDGET_KEY);
    }
}
