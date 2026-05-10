<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Azuriom\Plugin\Tebexrewards\Services\GoalProgressService;
use Azuriom\Plugin\Tebexrewards\Support\LeaderboardSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller {

    public function index(Request $request, GoalProgressService $goal) {
        abort_if((bool) setting('tebexrewards.maintenance', false), 404);

        $opts = LeaderboardSettings::resolve($request);

        $cacheTtl = 600;
        $cacheKey = "tebexrewards.leaderboard.{$opts['period']}.{$opts['limit']}";

        $entries = Cache::remember($cacheKey, $cacheTtl, fn () => Transaction::leaderboard($opts['limit'], $opts['period']));

        return view('tebexrewards::public.leaderboard', [
            'period' => $opts['period'],
            'limit' => $opts['limit'],
            'columns' => $opts['columns'],
            'showMedals' => $opts['show_medals'],
            'showAvatars' => $opts['show_avatars'],
            'entries' => $entries,
            'goal' => Cache::remember('tebexrewards.widgets.goal', 60, fn () => $goal->get()),
            'lastEnabled' => (bool) setting('tebexrewards.last.enabled', true),
            'lastShowPackage' => (bool) setting('tebexrewards.last.show_package', true),
            'lastShowAmount' => (bool) setting('tebexrewards.last.show_amount', true),
            'lastTimestamp' => (string) setting('tebexrewards.last.timestamp', 'relative'),
            'lastAnimation' => (bool) setting('tebexrewards.last.animation', true),
            'lastAnimationSpeed' => (int) setting('tebexrewards.last.animation_speed', 800),
        ]);
    }
}

