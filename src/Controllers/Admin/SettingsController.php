<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Azuriom\Plugin\Tebexrewards\Requests\TebexRewardsSettingsRequest;
use Illuminate\Support\Facades\Crypt;

class SettingsController extends Controller
{
    public function show() {
        $encryptedApiKey = setting('tebexrewards.tebex_api_key', '');
        $apiKey = '';

        if ($encryptedApiKey !== '') {
            try {
                $apiKey = Crypt::decryptString($encryptedApiKey);
            } catch (\Throwable $e) {
                $apiKey = '';
            }
        }

        return view('tebexrewards::admin.settings', [
            'api_key' => $apiKey,
            'sync_interval' => (int) setting('tebexrewards.sync_interval', 10),
            'leaderboard_limit' => (int) setting('tebexrewards.leaderboard.limit', 10),
            'leaderboard_period' => (string) setting('tebexrewards.leaderboard.period', 'all'),
            'leaderboard_columns' => (array) json_decode((string) setting('tebexrewards.leaderboard.columns', '[]'), true),
            'leaderboard_medals' => (bool) setting('tebexrewards.leaderboard.medals', true),
            'leaderboard_avatars' => (bool) setting('tebexrewards.leaderboard.avatars', true),
            'maintenance_mode' => (bool) setting('tebexrewards.maintenance', false),

            'goal_enabled' => (bool) setting('tebexrewards.goal.enabled', true),
            'goal_target' => (float) setting('tebexrewards.goal.target', 3000),
            'goal_currency' => (string) setting('tebexrewards.goal.currency', '€'),
            'goal_color_start' => (string) setting('tebexrewards.goal.color_start', '#00c853'),
            'goal_color_mid' => (string) setting('tebexrewards.goal.color_mid', '#ffd600'),
            'goal_color_end' => (string) setting('tebexrewards.goal.color_end', '#ff3d00'),
            'goal_message_reached' => (string) setting('tebexrewards.goal.message_reached', trans('tebexrewards::messages.defaults.goal_message_reached')),
            'goal_reset_enabled' => (bool) setting('tebexrewards.goal.reset_enabled', false),
            'goal_reset_increment' => (float) setting('tebexrewards.goal.reset_increment', 0),

            'last_enabled' => (bool) setting('tebexrewards.last.enabled', true),
            'last_show_package' => (bool) setting('tebexrewards.last.show_package', true),
            'last_show_amount' => (bool) setting('tebexrewards.last.show_amount', true),
            'last_timestamp' => (string) setting('tebexrewards.last.timestamp', 'relative'),
            'last_animation' => (bool) setting('tebexrewards.last.animation', true),
            'last_animation_speed' => (int) setting('tebexrewards.last.animation_speed', 800),
        ]);
    }

    public function save(TebexRewardsSettingsRequest $request) {
        $apiKey = (string) $request->input('tebex_api_key', '');
        $encryptedApiKey = $apiKey !== '' ? Crypt::encryptString($apiKey) : '';

        $columns = $request->input('leaderboard_columns', []);
        if (! is_array($columns)) {
            $columns = [];
        }

        Setting::updateSettings([
            'tebexrewards.tebex_api_key' => $encryptedApiKey,
            'tebexrewards.sync_interval' => (int) $request->input('sync_interval'),
            'tebexrewards.maintenance' => (bool) $request->boolean('maintenance_mode'),

            'tebexrewards.leaderboard.limit' => (int) $request->input('leaderboard_limit'),
            'tebexrewards.leaderboard.period' => (string) $request->input('leaderboard_period'),
            'tebexrewards.leaderboard.columns' => json_encode(array_values($columns), JSON_UNESCAPED_UNICODE),
            'tebexrewards.leaderboard.medals' => (bool) $request->boolean('leaderboard_medals'),
            'tebexrewards.leaderboard.avatars' => (bool) $request->boolean('leaderboard_avatars'),

            'tebexrewards.goal.enabled' => (bool) $request->boolean('goal_enabled'),
            'tebexrewards.goal.target' => (float) $request->input('goal_target'),
            'tebexrewards.goal.currency' => (string) $request->input('goal_currency'),
            'tebexrewards.goal.color_start' => (string) $request->input('goal_color_start'),
            'tebexrewards.goal.color_mid' => (string) $request->input('goal_color_mid'),
            'tebexrewards.goal.color_end' => (string) $request->input('goal_color_end'),
            'tebexrewards.goal.message_reached' => (string) $request->input('goal_message_reached'),
            'tebexrewards.goal.reset_enabled' => (bool) $request->boolean('goal_reset_enabled'),
            'tebexrewards.goal.reset_increment' => (float) $request->input('goal_reset_increment'),

            'tebexrewards.last.enabled' => (bool) $request->boolean('last_enabled'),
            'tebexrewards.last.show_package' => (bool) $request->boolean('last_show_package'),
            'tebexrewards.last.show_amount' => (bool) $request->boolean('last_show_amount'),
            'tebexrewards.last.timestamp' => (string) $request->input('last_timestamp'),
            'tebexrewards.last.animation' => (bool) $request->boolean('last_animation'),
            'tebexrewards.last.animation_speed' => (int) $request->input('last_animation_speed'),
        ]);

        return redirect()
            ->route('tebexrewards.admin.settings')
            ->with('success', trans('admin.settings.updated'));
    }
}

