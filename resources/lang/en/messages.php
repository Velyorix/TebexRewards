<?php

return [
    'defaults' => [
        'goal_message_reached' => '🎉 Goal reached! Thank you everyone! 🎉',
    ],

    'route_descriptions' => [
        'hub' => 'Tebex Rewards — donations hub',
        'leaderboard_only' => 'Donor leaderboard (table only)',
    ],

    'nav' => [
        'leaderboard' => 'Donor leaderboard',
        'hub' => 'Donations hub',
        'leaderboard_only' => 'Leaderboard only',
    ],

    'hub' => [
        'title' => 'Tebex Rewards',
        'intro' => 'Goal progress, last purchaser and donor leaderboard on one page.',
        'leaderboard_standalone_cta' => 'Open leaderboard-only page',
        'leaderboard_standalone_hint' => 'embed-friendly page with just the ranking table.',
    ],

    'period' => [
        'all' => 'All-time',
        'month' => 'This month',
        'week' => 'This week',
        'day' => 'Today',
    ],

    'leaderboard' => [
        'title' => 'Donor leaderboard',
        'page_standalone_title' => 'Donor leaderboard',
        'back_to_hub' => '← Back to donations hub',
        'redirecting' => 'Redirecting…',
        'period' => 'Period',
        'limit' => 'Displayed',
        'limit_locked' => 'The number of players is configured by the server.',
        'empty' => 'No transactions yet.',
        'columns' => [
            'rank' => 'Rank',
            'player' => 'Player',
            'amount' => 'Total amount',
            'purchases' => 'Purchases',
        ],
    ],

    'actions' => [
        'apply' => 'Apply',
    ],

    'goal' => [
        'title' => 'Goal progress',
        'percent' => ':percent%',
    ],

    'last' => [
        'title' => 'Last purchaser',
        'loading' => 'Loading…',
        'empty' => 'No purchase yet.',
        'spent' => 'spent :amount',
        'on_package' => 'on ":package"',
        'seconds_ago' => ':seconds seconds ago',
        'minutes_ago' => ':minutes minutes ago',
        'hours_ago' => ':hours hours ago',
        'days_ago' => ':days days ago',
    ],

    'admin' => [
        'title' => 'Tebex Rewards',
        'welcome' => 'Configure Tebex Rewards from the settings page.',
        'sync_forced' => 'Headless API cache refreshed.',
        'nav' => [
            'settings' => 'Settings',
            'leaderboard' => 'Leaderboard',
            'progress' => 'Goal progress',
            'ranks' => 'Ranks',
        ],
        'pages' => [
            'leaderboard_hint' => 'Leaderboard settings are managed in the Settings page.',
            'leaderboard_title' => 'Leaderboard overview',
            'leaderboard_intro' => 'Live preview of the ranking for the selected period, plus stats and public URLs.',
            'leaderboard_settings_btn' => 'Leaderboard options',
            'goto_leaderboard_admin' => 'Admin leaderboard page',
            'progress_hint' => 'Use the Goal progress admin page for a live preview, or edit all options in Settings.',
            'ranks_hint' => 'Manage supporter tiers (add, edit, delete, reset defaults) from the Ranks page.',
        ],
        'leaderboard' => [
            'stats_unique_donors' => 'Unique donors',
            'stats_transactions' => 'Completed payments',
            'stats_volume' => 'Volume (period)',
            'preview_title' => 'Preview',
            'preview_footer' => 'Showing top :n players according to settings.',
            'links_title' => 'URLs for Tebex & integration',
            'link_hub' => 'Public hub (/tebexrewards)',
            'link_standalone' => 'Leaderboard-only page (/tebexrewards/leaderboard)',
            'link_webhook' => 'Webhook endpoint (POST)',
            'link_widget_json' => 'Leaderboard JSON widget',
        ],
        'cache_cleared' => 'Tebex Rewards cache cleared (leaderboard and widgets).',

        'quick_actions' => [
            'title' => 'Quick actions',
            'intro' => 'These actions run immediately and do not replace saving settings.',
            'sync_help' => 'Force sync refreshes cached Headless API metadata (categories, packages). It does not import historical payments — webhooks handle live purchases.',
        ],

        'actions' => [
            'clear_cache' => 'Clear cache',
            'clear_cache_help' => 'Clears cached leaderboard, last purchaser and goal bar payloads (does not delete rows in tebex_transactions). Use after data fixes or if values look stale.',
            'copy' => 'Copy',
            'force_sync' => 'Force Headless sync',
        ],

        'settings' => [
            'title' => 'Settings',
            'sections' => [
                'general' => 'General',
                'leaderboard' => 'Leaderboard',
                'goal' => 'Goal progress bar',
                'last' => 'Last purchaser',
            ],
            'tabs_help' => [
                'general' => 'API credentials, webhook secret, maintenance, and user-menu links.',
                'leaderboard' => 'How the ranking table looks and refreshes on public pages.',
                'goal' => 'Financial goal bar colours and optional reset cycle.',
                'last' => 'Last purchaser widget behaviour.',
            ],
            'user_nav_title' => 'User menu (navbar)',
            'user_nav_help' => 'Choose which Tebex Rewards items appear in the logged-in user menu. Public URLs still work if someone has the link.',
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
            'webhook_endpoint_url' => 'Webhook URL (paste in Tebex)',
            'webhook_endpoint_help' => 'Configure this exact URL in your Tebex project webhook settings.',
            'sync_interval' => 'Sync interval',
            'sync_interval_help' => 'How often the scheduler runs tebexrewards:sync (Headless metadata cache).',
            'maintenance' => 'Maintenance mode (disable public modules)',

            'nav_user_hub' => 'Show “Donations hub” in the user menu',
            'nav_user_hub_help' => 'Link to /tebexrewards (goal, last purchaser, leaderboard).',
            'nav_user_leaderboard' => 'Show “Leaderboard only” in the user menu',
            'nav_user_leaderboard_help' => 'Link to /tebexrewards/leaderboard (table only).',

            'leaderboard_limit' => 'Players to display',
            'leaderboard_period' => 'Default period',
            'leaderboard_columns' => 'Displayed columns',
            'leaderboard_medals' => 'Show medals for top 3',
            'leaderboard_avatars' => 'Show avatars',
            'leaderboard_poll_seconds' => 'Auto-refresh interval',
            'leaderboard_poll_option' => 'Every :seconds s',
            'leaderboard_poll_help' => 'How often the public leaderboard table polls the JSON widget (live updates).',
            'leaderboard_layout' => 'Table layout',
            'leaderboard_layout_default' => 'Default (comfortable)',
            'leaderboard_layout_compact' => 'Compact (dense table)',
            'leaderboard_layout_help' => 'Matches the CDC “template” option — compact uses a tighter table style.',

            'goal_enabled' => 'Enable goal progress bar',
            'goal_target' => 'Target amount',
            'goal_currency' => 'Currency',
            'goal_color_start' => 'Start color (#hex)',
            'goal_color_mid' => 'Middle color (#hex)',
            'goal_color_end' => 'End color (#hex)',
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

        'progress' => [
            'title' => 'Goal progress',
            'intro' => 'Preview how the goal bar looks on the public hub. Full configuration is under Settings → Goal.',
            'open_settings' => 'Edit in settings',
            'view_public' => 'Open public hub',
            'preview_title' => 'Live preview',
            'preview_footer' => 'Amounts are based on completed Tebex payments recorded in this Azuriom database.',
        ],

        'ranks' => [
            'title' => 'Supporter ranks',
            'intro' => 'Define tiers by minimum lifetime donation. The highest matching tier is shown on the user profile (default Azuriom profile layout).',
            'section_profile' => 'Profile card',
            'section_profile_help' => 'When enabled, logged-in users see their total donated and best rank on the profile page (Bootstrap default theme). Custom themes can include tebexrewards::user.profile_snippet.',
            'profile_card_enabled' => 'Show Tebex Rewards card on user profile',
            'link_goal_settings' => 'Goal & currency',
            'form_add' => 'Add a tier',
            'form_edit' => 'Edit tier',
            'cancel_edit' => 'Cancel edit',
            'table_title' => 'Configured tiers',
            'actions' => 'Actions',
            'empty' => 'No tiers yet. Add one or reset to CDC defaults.',
            'reset_defaults' => 'Reset to CDC defaults',
            'reset_confirm' => 'Replace all tiers with the default 7 tiers from the specification? Current rows will be deleted.',
            'delete_confirm' => 'Delete this tier?',
            'icon_help' => 'Emoji or full image URL.',
            'created' => 'Tier created.',
            'updated' => 'Tier updated.',
            'deleted' => 'Tier deleted.',
            'reset_done' => 'Default tiers restored.',
            'fields' => [
                'name' => 'Rank name',
                'min_amount' => 'Minimum amount',
                'icon' => 'Icon',
                'color_hex' => 'Badge colour (#hex)',
                'enabled' => 'Active',
            ],
        ],
    ],

    'profile' => [
        'card_title' => 'Tebex donations',
        'total_label' => 'Total donated (all-time)',
        'no_tier' => 'No supporter tier reached yet.',
        'tier_hint' => 'The highest tier you qualify for is shown (lifetime total, completed payments only).',
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
