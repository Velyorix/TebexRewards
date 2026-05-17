@extends('admin.layouts.admin')

@section('title', trans('tebexrewards::messages.admin.settings.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <h1 class="h3 mb-0">{{ trans('tebexrewards::messages.admin.settings.title') }}</h1>
                <a href="{{ route('tebexrewards.admin.logs') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-journal-text"></i> {{ trans('tebexrewards::messages.admin.nav.logs') }}
                </a>
            </div>

            <ul class="nav nav-tabs mb-0" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-general-btn" data-bs-toggle="tab" data-bs-target="#pane-general" type="button" role="tab">{{ trans('tebexrewards::messages.admin.settings.sections.general') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-leaderboard-btn" data-bs-toggle="tab" data-bs-target="#pane-leaderboard" type="button" role="tab">{{ trans('tebexrewards::messages.admin.settings.sections.leaderboard') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-goal-btn" data-bs-toggle="tab" data-bs-target="#pane-goal" type="button" role="tab">{{ trans('tebexrewards::messages.admin.settings.sections.goal') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-last-btn" data-bs-toggle="tab" data-bs-target="#pane-last" type="button" role="tab">{{ trans('tebexrewards::messages.admin.settings.sections.last') }}</button>
                </li>
            </ul>

            <form id="headlessTokenTestForm" action="{{ route('tebexrewards.admin.settings.test_token') }}" method="POST" class="d-none">
                @csrf
                <input type="hidden" name="headless_public_token" id="testPublicTokenValue" value="">
                <input type="hidden" name="headless_private_key" id="testPrivateKeyValue" value="">
                <input type="hidden" name="headless_project_id" id="testProjectIdValue" value="">
            </form>

            <form action="{{ route('tebexrewards.admin.settings.save') }}" method="POST">
                @csrf

                <div class="tab-content border border-top-0 rounded-bottom p-4 bg-body">
                    <div class="tab-pane fade show active" id="pane-general" role="tabpanel">
                        <p class="text-muted small mb-4">{{ trans('tebexrewards::messages.admin.settings.tabs_help.general') }}</p>

                        <div class="rounded border p-3 mb-3">
                            <h2 class="h6 mb-2">{{ trans('tebexrewards::messages.admin.credentials.title') }}</h2>
                            <p class="text-muted small mb-3">{{ trans('tebexrewards::messages.admin.credentials.intro') }}</p>
                            <p class="small mb-3">
                                <a href="https://creator.tebex.io/developers/api-keys" target="_blank" rel="noopener noreferrer">
                                    {{ trans('tebexrewards::messages.admin.credentials.docs_link') }} <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </p>
                            @if($tebex_plugin_available && $tebex_plugin_credentials)
                                <div class="alert alert-info py-2 small mb-3">
                                    {{ trans('tebexrewards::messages.admin.credentials.tebex_plugin_hint') }}
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="importTebexPluginCredentials">
                                        {{ trans('tebexrewards::messages.admin.credentials.import_from_tebex') }}
                                    </button>
                                </div>
                                <script type="application/json" id="tebexPluginCredentialsJson">@json($tebex_plugin_credentials)</script>
                            @endif
                            <label class="form-label" for="headlessPublicTokenInput">{{ trans('tebexrewards::messages.admin.fields.headless_public_token') }}</label>
                            <input type="password" class="form-control @error('headless_public_token') is-invalid @enderror" id="headlessPublicTokenInput" name="headless_public_token" value="{{ old('headless_public_token', $headless_public_token) }}" autocomplete="new-password">
                            @error('headless_public_token')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            <small class="form-text text-muted d-block">{{ trans('tebexrewards::messages.admin.fields.headless_public_token_help') }}</small>
                            @if($tebex_plugin_available)
                                <small class="form-text text-muted d-block">{{ trans('tebexrewards::messages.admin.fields.headless_public_token_fallback') }}</small>
                            @endif
                            <div class="mt-3 mb-2">
                                <label class="form-label" for="headlessPrivateKeyInput">{{ trans('tebexrewards::messages.admin.fields.headless_private_key') }}</label>
                                <input type="password" class="form-control @error('headless_private_key') is-invalid @enderror" id="headlessPrivateKeyInput" name="headless_private_key" value="{{ old('headless_private_key') }}" autocomplete="new-password" placeholder="{{ $headless_private_key_configured ? trans('tebexrewards::messages.admin.credentials.secret_unchanged_placeholder') : '' }}">
                                @error('headless_private_key')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                <small class="form-text text-muted d-block">{{ trans('tebexrewards::messages.admin.fields.headless_private_key_help') }}</small>
                                @if($headless_private_key_configured)
                                    <small class="form-text text-success d-block"><i class="bi bi-check-circle"></i> {{ trans('tebexrewards::messages.admin.credentials.private_configured') }}</small>
                                @endif
                            </div>
                            <div class="mb-2">
                                <label class="form-label" for="headlessProjectIdInput">{{ trans('tebexrewards::messages.admin.fields.headless_project_id') }}</label>
                                <input type="text" class="form-control font-monospace @error('headless_project_id') is-invalid @enderror" id="headlessProjectIdInput" name="headless_project_id" value="{{ old('headless_project_id', $headless_project_id) }}" autocomplete="off">
                                @error('headless_project_id')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                <small class="form-text text-muted d-block">{{ trans('tebexrewards::messages.admin.fields.headless_project_id_help') }}</small>
                            </div>
                            <button type="submit" form="headlessTokenTestForm" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="bi bi-plug"></i> {{ trans('tebexrewards::messages.admin.actions.test_credentials') }}
                            </button>
                        </div>

                        <h2 class="h6 mb-2 mt-4">{{ trans('tebexrewards::messages.admin.credentials.webhook_section') }}</h2>
                        <div class="mb-3">
                            <label class="form-label" for="webhookSecretInput">{{ trans('tebexrewards::messages.admin.fields.webhook_secret') }}</label>
                            <input type="password" class="form-control @error('webhook_secret') is-invalid @enderror" id="webhookSecretInput" name="webhook_secret" value="{{ old('webhook_secret') }}" autocomplete="new-password" placeholder="{{ $webhook_secret_configured ? trans('tebexrewards::messages.admin.credentials.secret_unchanged_placeholder') : '' }}">
                            @error('webhook_secret')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            <small class="form-text text-muted d-block">{{ trans('tebexrewards::messages.admin.fields.webhook_secret_help') }}</small>
                            @if($webhook_secret_configured)
                                <small class="form-text text-success d-block"><i class="bi bi-check-circle"></i> {{ trans('tebexrewards::messages.admin.credentials.webhook_configured') }}</small>
                            @endif
                        </div>

                        <div class="rounded border bg-light p-3 mb-4">
                            <label class="form-label small fw-semibold">{{ trans('tebexrewards::messages.admin.fields.webhook_endpoint_url') }}</label>
                            <div class="input-group input-group-sm">
                                <input type="text" readonly class="form-control font-monospace" id="webhookEndpointDisplay" value="{{ url('/api/tebexrewards/webhook') }}">
                                <button type="button" class="btn btn-outline-secondary" id="copyWebhookEndpoint"><i class="bi bi-clipboard"></i> {{ trans('tebexrewards::messages.admin.actions.copy') }}</button>
                            </div>
                            <small class="form-text text-muted">{{ trans('tebexrewards::messages.admin.fields.webhook_endpoint_help') }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="syncIntervalSelect">{{ trans('tebexrewards::messages.admin.fields.sync_interval') }}</label>
                            <select class="form-select @error('sync_interval') is-invalid @enderror" id="syncIntervalSelect" name="sync_interval" required>
                                @foreach([5, 10, 30, 60] as $value)
                                    <option value="{{ $value }}" @selected((int) old('sync_interval', $sync_interval) === $value)>
                                        {{ trans('tebexrewards::messages.admin.sync_interval.minutes', ['minutes' => $value]) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sync_interval')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            <small class="form-text text-muted">{{ trans('tebexrewards::messages.admin.fields.sync_interval_help') }}</small>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="maintenanceSwitch" name="maintenance_mode" value="1" @checked((bool) old('maintenance_mode', $maintenance_mode))>
                            <label class="form-check-label" for="maintenanceSwitch">{{ trans('tebexrewards::messages.admin.fields.maintenance') }}</label>
                        </div>

                        <hr class="my-4">

                        <h2 class="h6 mb-2">{{ trans('tebexrewards::messages.admin.settings.user_nav_title') }}</h2>
                        <p class="text-muted small mb-3">{{ trans('tebexrewards::messages.admin.settings.user_nav_help') }}</p>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="navUserHubSwitch" name="nav_user_hub" value="1" @checked((bool) old('nav_user_hub', $nav_user_hub))>
                            <label class="form-check-label" for="navUserHubSwitch">{{ trans('tebexrewards::messages.admin.fields.nav_user_hub') }}</label>
                        </div>
                        <p class="text-muted small mb-3 ms-1">{{ trans('tebexrewards::messages.admin.fields.nav_user_hub_help') }}</p>

                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="navUserLeaderboardSwitch" name="nav_user_leaderboard" value="1" @checked((bool) old('nav_user_leaderboard', $nav_user_leaderboard))>
                            <label class="form-check-label" for="navUserLeaderboardSwitch">{{ trans('tebexrewards::messages.admin.fields.nav_user_leaderboard') }}</label>
                        </div>
                        <p class="text-muted small mb-0 ms-1">{{ trans('tebexrewards::messages.admin.fields.nav_user_leaderboard_help') }}</p>
                    </div>

                    <div class="tab-pane fade" id="pane-leaderboard" role="tabpanel">
                        <p class="text-muted small mb-4">{{ trans('tebexrewards::messages.admin.settings.tabs_help.leaderboard') }}</p>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="leaderboardLimitSelect">{{ trans('tebexrewards::messages.admin.fields.leaderboard_limit') }}</label>
                                <select class="form-select @error('leaderboard_limit') is-invalid @enderror" id="leaderboardLimitSelect" name="leaderboard_limit" required>
                                    @foreach([5, 10, 25, 50, 100] as $value)
                                        <option value="{{ $value }}" @selected((int) old('leaderboard_limit', $leaderboard_limit) === $value)>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('leaderboard_limit')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="leaderboardPeriodSelect">{{ trans('tebexrewards::messages.admin.fields.leaderboard_period') }}</label>
                                <select class="form-select @error('leaderboard_period') is-invalid @enderror" id="leaderboardPeriodSelect" name="leaderboard_period" required>
                                    @foreach(['all', 'month', 'week', 'day'] as $value)
                                        <option value="{{ $value }}" @selected((string) old('leaderboard_period', $leaderboard_period) === $value)>
                                            {{ trans('tebexrewards::messages.period.'.$value) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('leaderboard_period')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="leaderboardPollSelect">{{ trans('tebexrewards::messages.admin.fields.leaderboard_poll_seconds') }}</label>
                                <select class="form-select @error('leaderboard_poll_seconds') is-invalid @enderror" id="leaderboardPollSelect" name="leaderboard_poll_seconds" required>
                                    @foreach([5, 10, 15, 20, 30, 45, 60, 90, 120, 180, 300] as $sec)
                                        <option value="{{ $sec }}" @selected((int) old('leaderboard_poll_seconds', $leaderboard_poll_seconds) === $sec)>
                                            {{ trans('tebexrewards::messages.admin.fields.leaderboard_poll_option', ['seconds' => $sec]) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('leaderboard_poll_seconds')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                <small class="form-text text-muted">{{ trans('tebexrewards::messages.admin.fields.leaderboard_poll_help') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="leaderboardLayoutSelect">{{ trans('tebexrewards::messages.admin.fields.leaderboard_layout') }}</label>
                            <select class="form-select @error('leaderboard_layout') is-invalid @enderror" id="leaderboardLayoutSelect" name="leaderboard_layout" required>
                                <option value="default" @selected((string) old('leaderboard_layout', $leaderboard_layout) === 'default')>{{ trans('tebexrewards::messages.admin.fields.leaderboard_layout_default') }}</option>
                                <option value="compact" @selected((string) old('leaderboard_layout', $leaderboard_layout) === 'compact')>{{ trans('tebexrewards::messages.admin.fields.leaderboard_layout_compact') }}</option>
                            </select>
                            @error('leaderboard_layout')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            <small class="form-text text-muted">{{ trans('tebexrewards::messages.admin.fields.leaderboard_layout_help') }}</small>
                        </div>

                        @php
                            $selectedColumns = old('leaderboard_columns', $leaderboard_columns);
                            if (!is_array($selectedColumns)) $selectedColumns = [];
                        @endphp

                        <div class="mb-3">
                            <label class="form-label d-block">{{ trans('tebexrewards::messages.admin.fields.leaderboard_columns') }}</label>
                            @foreach(['rank', 'player', 'amount', 'purchases'] as $col)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="col_{{ $col }}" name="leaderboard_columns[]" value="{{ $col }}" @checked(in_array($col, $selectedColumns, true))>
                                    <label class="form-check-label" for="col_{{ $col }}">{{ trans('tebexrewards::messages.leaderboard.columns.'.$col) }}</label>
                                </div>
                            @endforeach
                            @error('leaderboard_columns')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="medalsSwitch" name="leaderboard_medals" value="1" @checked((bool) old('leaderboard_medals', $leaderboard_medals))>
                            <label class="form-check-label" for="medalsSwitch">{{ trans('tebexrewards::messages.admin.fields.leaderboard_medals') }}</label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="avatarsSwitch" name="leaderboard_avatars" value="1" @checked((bool) old('leaderboard_avatars', $leaderboard_avatars))>
                            <label class="form-check-label" for="avatarsSwitch">{{ trans('tebexrewards::messages.admin.fields.leaderboard_avatars') }}</label>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pane-goal" role="tabpanel">
                        <p class="text-muted small mb-4">{{ trans('tebexrewards::messages.admin.settings.tabs_help.goal') }}</p>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="goalEnabledSwitch" name="goal_enabled" value="1" @checked((bool) old('goal_enabled', $goal_enabled))>
                            <label class="form-check-label" for="goalEnabledSwitch">{{ trans('tebexrewards::messages.admin.fields.goal_enabled') }}</label>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="goalTargetInput">{{ trans('tebexrewards::messages.admin.fields.goal_target') }}</label>
                                <input type="number" min="0" step="0.01" class="form-control @error('goal_target') is-invalid @enderror" id="goalTargetInput" name="goal_target" value="{{ old('goal_target', $goal_target) }}" required>
                                @error('goal_target')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="goalCurrencyInput">{{ trans('tebexrewards::messages.admin.fields.goal_currency') }}</label>
                                <input type="text" class="form-control @error('goal_currency') is-invalid @enderror" id="goalCurrencyInput" name="goal_currency" value="{{ old('goal_currency', $goal_currency) }}" required>
                                @error('goal_currency')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="colorStartInput">{{ trans('tebexrewards::messages.admin.fields.goal_color_start') }}</label>
                                <input type="text" class="form-control font-monospace @error('goal_color_start') is-invalid @enderror" id="colorStartInput" name="goal_color_start" value="{{ old('goal_color_start', $goal_color_start) }}" required pattern="#[0-9A-Fa-f]{6}">
                                @error('goal_color_start')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="colorMidInput">{{ trans('tebexrewards::messages.admin.fields.goal_color_mid') }}</label>
                                <input type="text" class="form-control font-monospace @error('goal_color_mid') is-invalid @enderror" id="colorMidInput" name="goal_color_mid" value="{{ old('goal_color_mid', $goal_color_mid) }}" required pattern="#[0-9A-Fa-f]{6}">
                                @error('goal_color_mid')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="colorEndInput">{{ trans('tebexrewards::messages.admin.fields.goal_color_end') }}</label>
                                <input type="text" class="form-control font-monospace @error('goal_color_end') is-invalid @enderror" id="colorEndInput" name="goal_color_end" value="{{ old('goal_color_end', $goal_color_end) }}" required pattern="#[0-9A-Fa-f]{6}">
                                @error('goal_color_end')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="goalMessageInput">{{ trans('tebexrewards::messages.admin.fields.goal_message_reached') }}</label>
                            <input type="text" class="form-control @error('goal_message_reached') is-invalid @enderror" id="goalMessageInput" name="goal_message_reached" value="{{ old('goal_message_reached', $goal_message_reached) }}" required>
                            @error('goal_message_reached')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="goalResetSwitch" name="goal_reset_enabled" value="1" @checked((bool) old('goal_reset_enabled', $goal_reset_enabled))>
                            <label class="form-check-label" for="goalResetSwitch">{{ trans('tebexrewards::messages.admin.fields.goal_reset_enabled') }}</label>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="goalResetIncrementInput">{{ trans('tebexrewards::messages.admin.fields.goal_reset_increment') }}</label>
                            <input type="number" min="0" step="0.01" class="form-control @error('goal_reset_increment') is-invalid @enderror" id="goalResetIncrementInput" name="goal_reset_increment" value="{{ old('goal_reset_increment', $goal_reset_increment) }}" required>
                            @error('goal_reset_increment')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pane-last" role="tabpanel">
                        <p class="text-muted small mb-4">{{ trans('tebexrewards::messages.admin.settings.tabs_help.last') }}</p>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="lastEnabledSwitch" name="last_enabled" value="1" @checked((bool) old('last_enabled', $last_enabled))>
                            <label class="form-check-label" for="lastEnabledSwitch">{{ trans('tebexrewards::messages.admin.fields.last_enabled') }}</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="lastShowPackageSwitch" name="last_show_package" value="1" @checked((bool) old('last_show_package', $last_show_package))>
                            <label class="form-check-label" for="lastShowPackageSwitch">{{ trans('tebexrewards::messages.admin.fields.last_show_package') }}</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="lastShowAmountSwitch" name="last_show_amount" value="1" @checked((bool) old('last_show_amount', $last_show_amount))>
                            <label class="form-check-label" for="lastShowAmountSwitch">{{ trans('tebexrewards::messages.admin.fields.last_show_amount') }}</label>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="lastTimestampSelect">{{ trans('tebexrewards::messages.admin.fields.last_timestamp') }}</label>
                                <select class="form-select @error('last_timestamp') is-invalid @enderror" id="lastTimestampSelect" name="last_timestamp" required>
                                    @foreach(['relative', 'absolute'] as $value)
                                        <option value="{{ $value }}" @selected((string) old('last_timestamp', $last_timestamp) === $value)>
                                            {{ trans('tebexrewards::messages.admin.timestamps.'.$value) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('last_timestamp')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="lastAnimationSwitch" name="last_animation" value="1" @checked((bool) old('last_animation', $last_animation))>
                            <label class="form-check-label" for="lastAnimationSwitch">{{ trans('tebexrewards::messages.admin.fields.last_animation') }}</label>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="lastAnimationSpeedInput">{{ trans('tebexrewards::messages.admin.fields.last_animation_speed') }}</label>
                            <input type="number" min="100" step="50" class="form-control @error('last_animation_speed') is-invalid @enderror" id="lastAnimationSpeedInput" name="last_animation_speed" value="{{ old('last_animation_speed', $last_animation_speed) }}" required>
                            @error('last_animation_speed')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex flex-wrap gap-2 align-items-center">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ trans('messages.actions.save') }}</button>
                    <a href="{{ route('tebexrewards.admin.leaderboard') }}" class="btn btn-outline-secondary">{{ trans('tebexrewards::messages.admin.pages.goto_leaderboard_admin') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">{{ trans('tebexrewards::messages.admin.quick_actions.title') }}</h2>
            <p class="text-muted small mb-4">{{ trans('tebexrewards::messages.admin.quick_actions.intro') }}</p>
            <div class="d-flex flex-wrap gap-3">
                <form action="{{ route('tebexrewards.admin.settings.clear_cache') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">{{ trans('tebexrewards::messages.admin.actions.clear_cache') }}</button>
                </form>
                <form action="{{ route('tebexrewards.admin.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">{{ trans('tebexrewards::messages.admin.actions.force_sync') }}</button>
                </form>
            </div>
            <small class="form-text text-muted d-block mt-3">{{ trans('tebexrewards::messages.admin.quick_actions.sync_help') }}</small>
        </div>
    </div>

    <script>
        function tebexrewardsFillCredentialTestForm() {
            document.getElementById('testPublicTokenValue').value = document.getElementById('headlessPublicTokenInput').value;
            document.getElementById('testPrivateKeyValue').value = document.getElementById('headlessPrivateKeyInput').value;
            document.getElementById('testProjectIdValue').value = document.getElementById('headlessProjectIdInput').value;
        }

        document.getElementById('headlessTokenTestForm')?.addEventListener('submit', function () {
            tebexrewardsFillCredentialTestForm();
        });

        document.getElementById('importTebexPluginCredentials')?.addEventListener('click', function () {
            var node = document.getElementById('tebexPluginCredentialsJson');
            if (!node) return;
            try {
                var data = JSON.parse(node.textContent);
                if (data.public_token) document.getElementById('headlessPublicTokenInput').value = data.public_token;
                if (data.private_key) document.getElementById('headlessPrivateKeyInput').value = data.private_key;
                if (data.project_id) document.getElementById('headlessProjectIdInput').value = data.project_id;
            } catch (e) {}
        });

        document.getElementById('copyWebhookEndpoint')?.addEventListener('click', function () {
            var el = document.getElementById('webhookEndpointDisplay');
            if (el && navigator.clipboard) navigator.clipboard.writeText(el.value);
        });
        if (location.hash === '#pane-leaderboard') {
            document.querySelector('[data-bs-target="#pane-leaderboard"]')?.click();
        }
    </script>
@endsection
