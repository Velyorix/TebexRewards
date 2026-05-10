<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Azuriom\Plugin\Tebexrewards\Support\LeaderboardSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WidgetsController extends Controller {

    public function lastPurchaser(Request $request) {
        abort_if((bool) setting('tebexrewards.maintenance', false), 404);

        if (! (bool) setting('tebexrewards.last.enabled', true)) {
            return response()->json(['enabled' => false]);
        }

        $ttl = 30;
        $data = Cache::remember('tebexrewards.widgets.last_purchaser', $ttl, function () {
            $tx = Transaction::latestCompleted();
            if ($tx === null) {
                return null;
            }

            return [
                'player_name' => $tx->player_name,
                'amount' => (float) $tx->amount,
                'currency' => $tx->currency,
                'package_name' => $tx->package_name,
                'purchase_date' => $tx->purchase_date?->toISOString(),
            ];
        });

        return response()->json([
            'enabled' => true,
            'data' => $data,
        ]);
    }

    public function leaderboard(Request $request) {
        abort_if((bool) setting('tebexrewards.maintenance', false), 404);

        $opts = LeaderboardSettings::resolve($request);

        $entries = Transaction::leaderboard($opts['limit'], $opts['period'])
            ->map(function (array $entry) use ($opts) {
                $entry['amount_display'] = number_format((float) $entry['total_amount'], 2, '.', ' ');
                if ($opts['show_avatars']) {
                    $entry['avatar_url'] = tebexrewards_avatar_url($entry['player_name'], $entry['player_uuid'] ?? null, 24);
                }

                return $entry;
            })
            ->values();

        return response()->json([
            'period' => $opts['period'],
            'columns' => $opts['columns'],
            'show_medals' => $opts['show_medals'],
            'show_avatars' => $opts['show_avatars'],
            'entries' => $entries,
        ]);
    }
}

