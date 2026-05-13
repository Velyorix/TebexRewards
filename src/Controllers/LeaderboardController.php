<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Azuriom\Plugin\Tebexrewards\Services\GoalProgressService;
use Azuriom\Plugin\Tebexrewards\Support\LeaderboardSettings;
use Azuriom\Plugin\Tebexrewards\Support\TebexRewardsCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller {

    public function index(Request $request, GoalProgressService $goal) {
        abort_if((bool) setting('tebexrewards.maintenance', false), 404);

        $lb = $this->leaderboardViewData($request);

        return view('tebexrewards::public.leaderboard', array_merge($lb, [
            'goal' => Cache::remember(
                TebexRewardsCache::GOAL_WIDGET_KEY,
                TebexRewardsCache::TTL_GOAL_WIDGET_SECONDS,
                fn () => $goal->get()
            ),
            'lastEnabled' => (bool) setting('tebexrewards.last.enabled', true),
            'lastShowPackage' => (bool) setting('tebexrewards.last.show_package', true),
            'lastShowAmount' => (bool) setting('tebexrewards.last.show_amount', true),
            'lastTimestamp' => (string) setting('tebexrewards.last.timestamp', 'relative'),
            'lastAnimation' => (bool) setting('tebexrewards.last.animation', true),
            'lastAnimationSpeed' => (int) setting('tebexrewards.last.animation_speed', 800),
            'formActionRoute' => 'tebexrewards.index',
        ]));
    }

    public function leaderboardOnly(Request $request) {
        abort_if((bool) setting('tebexrewards.maintenance', false), 404);

        return view(
            'tebexrewards::public.leaderboard_standalone',
            array_merge($this->leaderboardViewData($request), [
                'formActionRoute' => 'tebexrewards.leaderboard',
            ])
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function leaderboardViewData(Request $request): array {
        $opts = LeaderboardSettings::resolve($request);

        $cacheKey = TebexRewardsCache::leaderboardPageKey($opts['period'], $opts['limit']);

        $entries = Cache::remember(
            $cacheKey,
            TebexRewardsCache::TTL_LEADERBOARD_PAGE_SECONDS,
            fn () => Transaction::leaderboard($opts['limit'], $opts['period'])
        );

        $pollSeconds = (int) setting('tebexrewards.leaderboard.poll_seconds', 10);
        $pollSeconds = max(5, min(300, $pollSeconds));

        $layout = (string) setting('tebexrewards.leaderboard.layout', 'default');
        if (! in_array($layout, ['default', 'compact'], true)) {
            $layout = 'default';
        }

        $tableDensity = $layout === 'compact' ? 'table-sm' : '';

        return [
            'period' => $opts['period'],
            'limit' => $opts['limit'],
            'columns' => $opts['columns'],
            'showMedals' => $opts['show_medals'],
            'showAvatars' => $opts['show_avatars'],
            'entries' => $entries,
            'pollSeconds' => $pollSeconds,
            'tableDensity' => $tableDensity,
        ];
    }
}
