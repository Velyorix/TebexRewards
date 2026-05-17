<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Azuriom\Plugin\Tebexrewards\Requests\TebexRewardsSettingsRequest;
use Azuriom\Plugin\Tebexrewards\Services\EventLogService;
use Azuriom\Plugin\Tebexrewards\Services\SyncService;
use Azuriom\Plugin\Tebexrewards\Services\TebexApiService;
use Azuriom\Plugin\Tebexrewards\Support\TebexCredentials;
use Azuriom\Plugin\Tebexrewards\Support\TebexRewardsCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show()
    {
        $tebexPlugin = plugins()->isEnabled('tebex');

        return view('tebexrewards::admin.settings', [
            'headless_public_token' => TebexCredentials::publicToken(),
            'headless_private_key_configured' => TebexCredentials::hasPrivateKey(),
            'headless_project_id' => TebexCredentials::projectId(),
            'webhook_secret_configured' => TebexCredentials::hasWebhookSecret(),
            'tebex_plugin_available' => $tebexPlugin,
            'tebex_plugin_credentials' => $tebexPlugin ? TebexCredentials::fromTebexPlugin() : null,
            'sync_interval' => (int) setting('tebexrewards.sync_interval', 10),
            'leaderboard_limit' => (int) setting('tebexrewards.leaderboard.limit', 10),
            'leaderboard_period' => (string) setting('tebexrewards.leaderboard.period', 'all'),
            'leaderboard_columns' => (array) json_decode((string) setting('tebexrewards.leaderboard.columns', '[]'), true),
            'leaderboard_medals' => (bool) setting('tebexrewards.leaderboard.medals', true),
            'leaderboard_avatars' => (bool) setting('tebexrewards.leaderboard.avatars', true),
            'leaderboard_poll_seconds' => (int) setting('tebexrewards.leaderboard.poll_seconds', 10),
            'leaderboard_layout' => (string) setting('tebexrewards.leaderboard.layout', 'default'),
            'maintenance_mode' => (bool) setting('tebexrewards.maintenance', false),
            'nav_user_hub' => (bool) setting('tebexrewards.nav.user_hub', true),
            'nav_user_leaderboard' => (bool) setting('tebexrewards.nav.user_leaderboard', true),

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

    public function save(TebexRewardsSettingsRequest $request)
    {
        $encryptedPublic = $this->persistEncryptedSecret(
            trim((string) $request->input('headless_public_token', '')),
            TebexCredentials::SETTING_PUBLIC_TOKEN,
            TebexCredentials::SETTING_PUBLIC_TOKEN_LEGACY
        );

        $encryptedPrivate = $this->persistEncryptedSecret(
            trim((string) $request->input('headless_private_key', '')),
            TebexCredentials::SETTING_PRIVATE_KEY
        );

        $projectId = trim((string) $request->input('headless_project_id', ''));

        $webhookSecret = trim((string) $request->input('webhook_secret', ''));
        $encryptedWebhook = $this->persistEncryptedSecret(
            $webhookSecret,
            TebexCredentials::SETTING_WEBHOOK_SECRET
        );

        $columns = $request->input('leaderboard_columns', []);
        if (! is_array($columns)) {
            $columns = [];
        }

        Setting::updateSettings([
            TebexCredentials::SETTING_PUBLIC_TOKEN => $encryptedPublic,
            TebexCredentials::SETTING_PUBLIC_TOKEN_LEGACY => $encryptedPublic,
            TebexCredentials::SETTING_PRIVATE_KEY => $encryptedPrivate,
            TebexCredentials::SETTING_PROJECT_ID => $projectId,
            TebexCredentials::SETTING_WEBHOOK_SECRET => $encryptedWebhook,
            'tebexrewards.sync_interval' => (int) $request->input('sync_interval'),
            'tebexrewards.maintenance' => (bool) $request->boolean('maintenance_mode'),
            'tebexrewards.nav.user_hub' => (bool) $request->boolean('nav_user_hub'),
            'tebexrewards.nav.user_leaderboard' => (bool) $request->boolean('nav_user_leaderboard'),

            'tebexrewards.leaderboard.limit' => (int) $request->input('leaderboard_limit'),
            'tebexrewards.leaderboard.period' => (string) $request->input('leaderboard_period'),
            'tebexrewards.leaderboard.columns' => json_encode(array_values($columns), JSON_UNESCAPED_UNICODE),
            'tebexrewards.leaderboard.medals' => (bool) $request->boolean('leaderboard_medals'),
            'tebexrewards.leaderboard.avatars' => (bool) $request->boolean('leaderboard_avatars'),
            'tebexrewards.leaderboard.poll_seconds' => (int) $request->input('leaderboard_poll_seconds'),
            'tebexrewards.leaderboard.layout' => (string) $request->input('leaderboard_layout'),

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

        TebexRewardsCache::invalidateAll();

        return redirect()
            ->route('tebexrewards.admin.settings')
            ->with('success', trans('admin.settings.updated'));
    }

    public function clearCache()
    {
        TebexRewardsCache::invalidateAll();

        return redirect()
            ->route('tebexrewards.admin.settings')
            ->with('success', trans('tebexrewards::messages.admin.cache_cleared'));
    }

    public function syncNow(AdminController $admin, SyncService $syncService, EventLogService $eventLogs): RedirectResponse
    {
        return $admin->syncHeadless($syncService, $eventLogs);
    }

    public function testHeadlessToken(Request $request, TebexApiService $api): RedirectResponse
    {
        $request->validate([
            'headless_public_token' => ['required', 'string', 'max:255'],
            'headless_private_key' => ['nullable', 'string', 'max:512'],
            'headless_project_id' => ['nullable', 'string', 'max:64'],
        ]);

        $public = trim((string) $request->input('headless_public_token'));
        $private = trim((string) $request->input('headless_private_key'));
        $projectId = trim((string) $request->input('headless_project_id'));

        try {
            $payload = $api->validatePublicToken($public);
            $name = $payload['data']['name'] ?? $payload['name'] ?? null;
            $message = trans('tebexrewards::messages.admin.token_test_success', [
                'name' => is_string($name) ? $name : '—',
            ]);

            if ($private !== '' || TebexCredentials::hasPrivateKey()) {
                $privateCheck = $api->validatePrivateKey(
                    $private !== '' ? $private : null,
                    $public,
                    $projectId !== '' ? $projectId : null
                );
                $message .= ' '.$privateCheck['detail'];
            }

            return redirect()
                ->route('tebexrewards.admin.settings')
                ->withInput()
                ->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()
                ->route('tebexrewards.admin.settings')
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    private function persistEncryptedSecret(string $plain, string $primaryKey, ?string $legacyKey = null): string
    {
        if ($plain !== '') {
            $encrypted = TebexCredentials::encrypt($plain);

            return $encrypted;
        }

        $existing = (string) setting($primaryKey, '');
        if ($existing === '' && $legacyKey !== null) {
            $existing = (string) setting($legacyKey, '');
        }

        return $existing;
    }

}
