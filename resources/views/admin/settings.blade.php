@extends('admin.layouts.admin')

@section('title', trans('tebexrewards::messages.admin.settings.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('tebexrewards.admin.settings.save') }}" method="POST">
                @csrf

                <h2 class="h4">{{ trans('tebexrewards::messages.admin.settings.sections.general') }}</h2>

                <div class="mb-3">
                    <label class="form-label" for="apiKeyInput">{{ trans('tebexrewards::messages.admin.fields.tebex_api_key') }}</label>
                    <input
                        type="password"
                        class="form-control @error('tebex_api_key') is-invalid @enderror"
                        id="apiKeyInput"
                        name="tebex_api_key"
                        value="{{ old('tebex_api_key', $api_key) }}"
                        autocomplete="new-password"
                    >
                    @error('tebex_api_key')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                    <small class="form-text text-muted">{{ trans('tebexrewards::messages.admin.fields.tebex_api_key_help') }}</small>
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
                    @error('sync_interval')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="maintenanceSwitch" name="maintenance_mode" value="1"
                           @checked((bool) old('maintenance_mode', $maintenance_mode))>
                    <label class="form-check-label" for="maintenanceSwitch">{{ trans('tebexrewards::messages.admin.fields.maintenance') }}</label>
                </div>

                <hr class="my-4">

                <h2 class="h4">{{ trans('tebexrewards::messages.admin.settings.sections.leaderboard') }}</h2>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="leaderboardLimitSelect">{{ trans('tebexrewards::messages.admin.fields.leaderboard_limit') }}</label>
                        <select class="form-select @error('leaderboard_limit') is-invalid @enderror" id="leaderboardLimitSelect" name="leaderboard_limit" required>
                            @foreach([5, 10, 25, 50, 100] as $value)
                                <option value="{{ $value }}" @selected((int) old('leaderboard_limit', $leaderboard_limit) === $value)>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('leaderboard_limit')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
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
                        @error('leaderboard_period')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                @php
                    $selectedColumns = old('leaderboard_columns', $leaderboard_columns);
                    if (!is_array($selectedColumns)) $selectedColumns = [];
                @endphp

                <div class="mb-3">
                    <label class="form-label d-block">{{ trans('tebexrewards::messages.admin.fields.leaderboard_columns') }}</label>
                    @foreach(['rank', 'player', 'amount', 'purchases'] as $col)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="col_{{ $col }}" name="leaderboard_columns[]" value="{{ $col }}"
                                   @checked(in_array($col, $selectedColumns, true))>
                            <label class="form-check-label" for="col_{{ $col }}">{{ trans('tebexrewards::messages.leaderboard.columns.'.$col) }}</label>
                        </div>
                    @endforeach
                    @error('leaderboard_columns')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="medalsSwitch" name="leaderboard_medals" value="1"
                           @checked((bool) old('leaderboard_medals', $leaderboard_medals))>
                    <label class="form-check-label" for="medalsSwitch">{{ trans('tebexrewards::messages.admin.fields.leaderboard_medals') }}</label>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="avatarsSwitch" name="leaderboard_avatars" value="1"
                           @checked((bool) old('leaderboard_avatars', $leaderboard_avatars))>
                    <label class="form-check-label" for="avatarsSwitch">{{ trans('tebexrewards::messages.admin.fields.leaderboard_avatars') }}</label>
                </div>

                <hr class="my-4">

                <h2 class="h4">{{ trans('tebexrewards::messages.admin.settings.sections.goal') }}</h2>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="goalEnabledSwitch" name="goal_enabled" value="1"
                           @checked((bool) old('goal_enabled', $goal_enabled))>
                    <label class="form-check-label" for="goalEnabledSwitch">{{ trans('tebexrewards::messages.admin.fields.goal_enabled') }}</label>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="goalTargetInput">{{ trans('tebexrewards::messages.admin.fields.goal_target') }}</label>
                        <input type="number" min="0" step="0.01" class="form-control @error('goal_target') is-invalid @enderror"
                               id="goalTargetInput" name="goal_target" value="{{ old('goal_target', $goal_target) }}" required>
                        @error('goal_target')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="goalCurrencyInput">{{ trans('tebexrewards::messages.admin.fields.goal_currency') }}</label>
                        <input type="text" class="form-control @error('goal_currency') is-invalid @enderror"
                               id="goalCurrencyInput" name="goal_currency" value="{{ old('goal_currency', $goal_currency) }}" required>
                        @error('goal_currency')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="colorStartInput">{{ trans('tebexrewards::messages.admin.fields.goal_color_start') }}</label>
                        <input type="text" class="form-control @error('goal_color_start') is-invalid @enderror"
                               id="colorStartInput" name="goal_color_start" value="{{ old('goal_color_start', $goal_color_start) }}" required>
                        @error('goal_color_start')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="colorMidInput">{{ trans('tebexrewards::messages.admin.fields.goal_color_mid') }}</label>
                        <input type="text" class="form-control @error('goal_color_mid') is-invalid @enderror"
                               id="colorMidInput" name="goal_color_mid" value="{{ old('goal_color_mid', $goal_color_mid) }}" required>
                        @error('goal_color_mid')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="colorEndInput">{{ trans('tebexrewards::messages.admin.fields.goal_color_end') }}</label>
                        <input type="text" class="form-control @error('goal_color_end') is-invalid @enderror"
                               id="colorEndInput" name="goal_color_end" value="{{ old('goal_color_end', $goal_color_end) }}" required>
                        @error('goal_color_end')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="goalMessageInput">{{ trans('tebexrewards::messages.admin.fields.goal_message_reached') }}</label>
                    <input type="text" class="form-control @error('goal_message_reached') is-invalid @enderror"
                           id="goalMessageInput" name="goal_message_reached" value="{{ old('goal_message_reached', $goal_message_reached) }}" required>
                    @error('goal_message_reached')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="goalResetSwitch" name="goal_reset_enabled" value="1"
                           @checked((bool) old('goal_reset_enabled', $goal_reset_enabled))>
                    <label class="form-check-label" for="goalResetSwitch">{{ trans('tebexrewards::messages.admin.fields.goal_reset_enabled') }}</label>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="goalResetIncrementInput">{{ trans('tebexrewards::messages.admin.fields.goal_reset_increment') }}</label>
                    <input type="number" min="0" step="0.01" class="form-control @error('goal_reset_increment') is-invalid @enderror"
                           id="goalResetIncrementInput" name="goal_reset_increment" value="{{ old('goal_reset_increment', $goal_reset_increment) }}" required>
                    @error('goal_reset_increment')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <hr class="my-4">

                <h2 class="h4">{{ trans('tebexrewards::messages.admin.settings.sections.last') }}</h2>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="lastEnabledSwitch" name="last_enabled" value="1"
                           @checked((bool) old('last_enabled', $last_enabled))>
                    <label class="form-check-label" for="lastEnabledSwitch">{{ trans('tebexrewards::messages.admin.fields.last_enabled') }}</label>
                </div>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="lastShowPackageSwitch" name="last_show_package" value="1"
                           @checked((bool) old('last_show_package', $last_show_package))>
                    <label class="form-check-label" for="lastShowPackageSwitch">{{ trans('tebexrewards::messages.admin.fields.last_show_package') }}</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="lastShowAmountSwitch" name="last_show_amount" value="1"
                           @checked((bool) old('last_show_amount', $last_show_amount))>
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
                        @error('last_timestamp')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="lastAnimationSwitch" name="last_animation" value="1"
                           @checked((bool) old('last_animation', $last_animation))>
                    <label class="form-check-label" for="lastAnimationSwitch">{{ trans('tebexrewards::messages.admin.fields.last_animation') }}</label>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="lastAnimationSpeedInput">{{ trans('tebexrewards::messages.admin.fields.last_animation_speed') }}</label>
                    <input type="number" min="100" step="50" class="form-control @error('last_animation_speed') is-invalid @enderror"
                           id="lastAnimationSpeedInput" name="last_animation_speed" value="{{ old('last_animation_speed', $last_animation_speed) }}" required>
                    @error('last_animation_speed')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> {{ trans('messages.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection

