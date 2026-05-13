<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Azuriom\Plugin\Tebexrewards\Services\GoalProgressService;
use Illuminate\Http\Request;

class AdminController extends Controller {

    public function index()
    {
        return redirect()->route('tebexrewards.admin.settings');
    }

    public function leaderboard(Request $request) {

        $period = (string) $request->query('period', setting('tebexrewards.leaderboard.period', 'all'));
        if (! in_array($period, ['all', 'month', 'week', 'day'], true)) {
            $period = 'all';
        }

        $limit = max(5, min(100, (int) setting('tebexrewards.leaderboard.limit', 10)));

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

        $entries = Transaction::leaderboard($limit, $period);
        $stats = Transaction::donorStats($period);

        return view('tebexrewards::admin.leaderboard', [
            'period' => $period,
            'limit' => $limit,
            'columns' => $columns,
            'showMedals' => $showMedals,
            'showAvatars' => $showAvatars,
            'entries' => $entries,
            'stats' => $stats,
            'publicHubUrl' => route('tebexrewards.index'),
            'publicLeaderboardUrl' => route('tebexrewards.leaderboard'),
            'webhookUrl' => url('/api/tebexrewards/webhook'),
            'widgetJsonUrl' => url('/api/tebexrewards/widgets/leaderboard'),
        ]);
    }

    public function progress(GoalProgressService $goal) {

        return view('tebexrewards::admin.progress', [
            'goal' => $goal->get(),
            'hubUrl' => route('tebexrewards.index'),
            'settingsUrl' => route('tebexrewards.admin.settings').'#pane-goal',
        ]);
    }
}
