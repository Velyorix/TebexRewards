<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller {

    public function index(Request $request) {
        abort_if((bool) setting('tebexrewards.maintenance', false), 404);

        $limit = (int) setting('tebexrewards.leaderboard.limit', 10);
        $limit = max(5, min(100, $limit));

        $defaultPeriod = (string) setting('tebexrewards.leaderboard.period', 'all');
        $period = (string) $request->query('period', $defaultPeriod);
        if (! in_array($period, ['all', 'month', 'week', 'day'], true)) {
            $period = $defaultPeriod;
        }

        $columns = json_decode((string) setting('tebexrewards.leaderboard.columns', '["rank","player","amount","purchases"]'), true);
        if (! is_array($columns)) {
            $columns = ['rank', 'player', 'amount', 'purchases'];
        }
        $columns = array_values(array_intersect($columns, ['rank', 'player', 'amount', 'purchases']));
        if ($columns === []) {
            $columns = ['rank', 'player', 'amount', 'purchases'];
        }

        $showMedals = (bool) setting('tebexrewards.leaderboard.medals', true);
        $showAvatars = (bool) setting('tebexrewards.leaderboard.avatars', true);

        $cacheTtl = 600;
        $cacheKey = "tebexrewards.leaderboard.{$period}.{$limit}";

        $entries = Cache::remember($cacheKey, $cacheTtl, fn () => Transaction::leaderboard($limit, $period));

        return view('tebexrewards::public.leaderboard', [
            'period' => $period,
            'limit' => $limit,
            'columns' => $columns,
            'showMedals' => $showMedals,
            'showAvatars' => $showAvatars,
            'entries' => $entries,
        ]);
    }
}

