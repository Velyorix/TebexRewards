<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Tebexrewards\Models\EventLog;
use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Azuriom\Plugin\Tebexrewards\Services\EventLogService;
use Azuriom\Plugin\Tebexrewards\Support\TebexCredentials;
use Azuriom\Plugin\Tebexrewards\Services\GoalProgressService;
use Azuriom\Plugin\Tebexrewards\Services\SyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('tebexrewards.admin.logs');
    }

    public function logs(EventLogService $eventLogs): View
    {
        return $this->logsDashboard($eventLogs);
    }

    public function leaderboard(Request $request): View
    {
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

    public function progress(GoalProgressService $goal): View
    {
        return view('tebexrewards::admin.progress', [
            'goal' => $goal->get(),
            'hubUrl' => route('tebexrewards.index'),
            'settingsUrl' => route('tebexrewards.admin.settings').'#pane-goal',
        ]);
    }

    public function syncHeadless(SyncService $sync, EventLogService $eventLogs): RedirectResponse
    {
        try {
            $result = $sync->sync(true);

            $payments = $result['payments'] ?? [];
            $eventLogs->log(
                EventLog::CHANNEL_HEADLESS,
                'sync.success',
                trans('tebexrewards::messages.admin.logs.headless_success', [
                    'account' => $result['account_name'] ?? '—',
                    'categories' => $result['categories'],
                    'packages' => $result['packages'],
                    'payments_created' => (int) ($payments['created'] ?? 0),
                    'payments_updated' => (int) ($payments['updated'] ?? 0),
                ]),
                EventLog::LEVEL_INFO,
                $result
            );

            $flashMessage = trans('tebexrewards::messages.admin.sync_headless_success', [
                'account' => $result['account_name'] ?? '—',
                'categories' => $result['categories'],
                'packages' => $result['packages'],
                'payments_created' => (int) ($payments['created'] ?? 0),
                'payments_updated' => (int) ($payments['updated'] ?? 0),
            ]);

            if (($payments['skipped_missing_key'] ?? false) === true) {
                $flashMessage .= ' '.trans('tebexrewards::messages.admin.sync_payments_skipped_no_key');
            } elseif (($payments['error'] ?? null) !== null && ($payments['created'] ?? 0) === 0) {
                $flashMessage .= ' '.trans('tebexrewards::messages.admin.sync_payments_failed', [
                    'message' => (string) $payments['error'],
                ]);
            }

            return redirect()
                ->route('tebexrewards.admin.logs')
                ->with('success', $flashMessage);
        } catch (\Throwable $e) {
            report($e);

            $eventLogs->log(
                EventLog::CHANNEL_HEADLESS,
                'sync.failed',
                $e->getMessage(),
                EventLog::LEVEL_ERROR,
                ['exception' => $e::class]
            );

            return redirect()
                ->route('tebexrewards.admin.logs')
                ->with('error', trans('tebexrewards::messages.admin.sync_headless_failed', [
                    'message' => $e->getMessage(),
                ]));
        }
    }

    public function clearLogs(EventLogService $eventLogs): RedirectResponse
    {
        $deleted = $eventLogs->clearAll();

        return redirect()
            ->route('tebexrewards.admin.logs')
            ->with('success', trans('tebexrewards::messages.admin.logs.cleared', ['count' => $deleted]));
    }

    private function logsDashboard(EventLogService $eventLogs): View
    {
        return view('tebexrewards::admin.logs', [
            'logs' => $eventLogs->paginate(25),
            'stats' => $eventLogs->dashboardStats(),
            'logsTableReady' => $eventLogs->isAvailable(),
            'webhookUrl' => url('/api/tebexrewards/webhook'),
            'hasPublicToken' => TebexCredentials::hasPublicToken(),
            'hasPrivateKey' => TebexCredentials::hasPrivateKey(),
            'hasWebhookSecret' => TebexCredentials::hasWebhookSecret(),
        ]);
    }
}
