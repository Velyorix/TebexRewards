<?php

return [
    'defaults' => [
        'goal_message_reached' => '🎉 Goal reached! Thank you everyone! 🎉',
    ],

    'nav' => [
        'leaderboard' => 'Donor leaderboard',
    ],

    'period' => [
        'all' => 'All-time',
        'month' => 'This month',
        'week' => 'This week',
        'day' => 'Today',
    ],

    'leaderboard' => [
        'columns' => [
            'rank' => 'Rank',
            'player' => 'Player',
            'amount' => 'Total amount',
            'purchases' => 'Purchases',
        ],
    ],

    'admin' => [
        'title' => 'Tebex Rewards',
        'welcome' => 'Configure Tebex Rewards from the settings page.',
        'nav' => [
            'settings' => 'Settings',
            'leaderboard' => 'Leaderboard',
            'progress' => 'Goal progress',
            'ranks' => 'Ranks',
        ],
        'pages' => [
            'leaderboard_hint' => 'Leaderboard settings are managed in the Settings page.',
            'progress_hint' => 'Goal progress settings are managed in the Settings page.',
            'ranks_hint' => 'Rank tiers management will appear here once tiers are created.',
        ],
        'settings' => [
            'title' => 'Settings',
            'sections' => [
                'general' => 'General',
                'leaderboard' => 'Leaderboard',
                'goal' => 'Goal progress bar',
                'last' => 'Last purchaser',
            ],
        ],
        'sync_interval' => [
            'minutes' => ':minutes minutes',
        ],
        'timestamps' => [
            'relative' => 'Relative',
            'absolute' => 'Absolute',
        ],
        'fields' => [
            'tebex_api_key' => 'Tebex API key',
            'tebex_api_key_help' => 'Stored encrypted in settings. Required for sync and webhooks validation.',
            'webhook_secret' => 'Webhook secret',
            'webhook_secret_help' => 'Used to validate Tebex webhooks (HMAC SHA256 in X-Signature). Stored encrypted in settings.',
            'sync_interval' => 'Sync interval',
            'maintenance' => 'Maintenance mode (disable public modules)',

            'leaderboard_limit' => 'Players to display',
            'leaderboard_period' => 'Default period',
            'leaderboard_columns' => 'Displayed columns',
            'leaderboard_medals' => 'Show medals for top 3',
            'leaderboard_avatars' => 'Show avatars',

            'goal_enabled' => 'Enable goal progress bar',
            'goal_target' => 'Target amount',
            'goal_currency' => 'Currency',
            'goal_color_start' => 'Start color',
            'goal_color_mid' => 'Middle color',
            'goal_color_end' => 'End color',
            'goal_message_reached' => 'Reached message',
            'goal_reset_enabled' => 'Enable goal reset/loop',
            'goal_reset_increment' => 'Auto-increment amount after reset',

            'last_enabled' => 'Enable last purchaser',
            'last_show_package' => 'Show package name',
            'last_show_amount' => 'Show amount',
            'last_timestamp' => 'Timestamp format',
            'last_animation' => 'Enable animation',
            'last_animation_speed' => 'Animation speed (ms)',
        ],
    ],

    'console' => [
        'sync_done' => 'Sync completed: :synced synced, :skipped skipped.',
    ],

    'errors' => [
        'missing_api_key' => 'Tebex API key is missing.',
        'invalid_api_key' => 'Tebex API key is invalid.',
        'api_request_failed' => 'Tebex API request failed (HTTP :status).',
    ],

    'webhook' => [
        'ok' => 'Webhook accepted.',
        'invalid_signature' => 'Invalid signature.',
        'invalid_payload' => 'Invalid payload.',
        'missing_secret' => 'Webhook secret is missing.',
    ],
];
