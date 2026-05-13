<?php

namespace Azuriom\Plugin\Tebexrewards\Support;

use Illuminate\Http\Request;

final class LeaderboardSettings
{
    /**
     * @return array{limit:int, period:string, columns:array<int, string>, show_medals:bool, show_avatars:bool}
     */
    public static function resolve(Request $request): array {
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

        return [
            'limit' => $limit,
            'period' => $period,
            'columns' => $columns,
            'show_medals' => $showMedals,
            'show_avatars' => $showAvatars,
        ];
    }
}
