@extends('admin.layouts.admin')

@section('title', trans('tebexrewards::messages.admin.nav.leaderboard'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">{{ trans('tebexrewards::messages.admin.pages.leaderboard_title') }}</h1>
            <p class="text-muted mb-0">{{ trans('tebexrewards::messages.admin.pages.leaderboard_intro') }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('tebexrewards.admin.settings') }}#pane-leaderboard" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-gear"></i> {{ trans('tebexrewards::messages.admin.pages.leaderboard_settings_btn') }}
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ trans('tebexrewards::messages.admin.leaderboard.stats_unique_donors') }}</div>
                    <div class="fs-4 fw-semibold">{{ number_format($stats['unique_donors']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ trans('tebexrewards::messages.admin.leaderboard.stats_transactions') }}</div>
                    <div class="fs-4 fw-semibold">{{ number_format($stats['transactions_count']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ trans('tebexrewards::messages.admin.leaderboard.stats_volume') }}</div>
                    <div class="fs-4 fw-semibold">{{ number_format($stats['total_amount'], 2, '.', ' ') }} {{ (string) setting('tebexrewards.goal.currency', '€') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">{{ trans('tebexrewards::messages.admin.leaderboard.preview_title') }}</span>
            <form method="GET" action="{{ route('tebexrewards.admin.leaderboard') }}" class="d-flex align-items-center gap-2">
                <label class="small mb-0 text-muted" for="adminLbPeriod">{{ trans('tebexrewards::messages.leaderboard.period') }}</label>
                <select id="adminLbPeriod" name="period" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    @foreach(['all', 'month', 'week', 'day'] as $p)
                        <option value="{{ $p }}" @selected($period === $p)>{{ trans('tebexrewards::messages.period.'.$p) }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    @foreach($columns as $col)
                        <th>{{ trans('tebexrewards::messages.leaderboard.columns.'.$col) }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @forelse($entries as $entry)
                    <tr>
                        @foreach($columns as $col)
                            @if($col === 'rank')
                                <td>
                                    @php($r = (int) $entry['rank'])
                                    @if($showMedals && $r <= 3)
                                        @if($r === 1) 🥇 @elseif($r === 2) 🥈 @else 🥉 @endif
                                    @endif
                                    {{ $r }}
                                </td>
                            @elseif($col === 'player')
                                <td class="d-flex align-items-center gap-2">
                                    @if($showAvatars)
                                        <img src="{{ tebexrewards_avatar_url($entry['player_name'], $entry['player_uuid'] ?? null, 28) }}" width="28" height="28" class="rounded" alt="" loading="lazy">
                                    @endif
                                    {{ $entry['player_name'] }}
                                </td>
                            @elseif($col === 'amount')
                                <td>{{ number_format((float) $entry['total_amount'], 2, '.', ' ') }}</td>
                            @elseif($col === 'purchases')
                                <td>{{ (int) $entry['purchases_count'] }}</td>
                            @endif
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="text-center text-muted py-5">{{ trans('tebexrewards::messages.leaderboard.empty') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer small text-muted">
            {{ trans('tebexrewards::messages.admin.leaderboard.preview_footer', ['n' => $limit]) }}
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header fw-semibold">{{ trans('tebexrewards::messages.admin.leaderboard.links_title') }}</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label small text-muted mb-1">{{ trans('tebexrewards::messages.admin.leaderboard.link_hub') }}</label>
                    <div class="input-group input-group-sm">
                        <input type="text" readonly class="form-control font-monospace" value="{{ $publicHubUrl }}">
                        <button type="button" class="btn btn-outline-secondary" data-copy="{{ $publicHubUrl }}" title="{{ trans('tebexrewards::messages.admin.actions.copy') }}"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label class="form-label small text-muted mb-1">{{ trans('tebexrewards::messages.admin.leaderboard.link_standalone') }}</label>
                    <div class="input-group input-group-sm">
                        <input type="text" readonly class="form-control font-monospace" value="{{ $publicLeaderboardUrl }}">
                        <button type="button" class="btn btn-outline-secondary" data-copy="{{ $publicLeaderboardUrl }}"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label class="form-label small text-muted mb-1">{{ trans('tebexrewards::messages.admin.leaderboard.link_webhook') }}</label>
                    <div class="input-group input-group-sm">
                        <input type="text" readonly class="form-control font-monospace" value="{{ $webhookUrl }}">
                        <button type="button" class="btn btn-outline-secondary" data-copy="{{ $webhookUrl }}"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label class="form-label small text-muted mb-1">{{ trans('tebexrewards::messages.admin.leaderboard.link_widget_json') }}</label>
                    <div class="input-group input-group-sm">
                        <input type="text" readonly class="form-control font-monospace" value="{{ $widgetJsonUrl }}">
                        <button type="button" class="btn btn-outline-secondary" data-copy="{{ $widgetJsonUrl }}"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-copy]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const v = btn.getAttribute('data-copy');
                if (v && navigator.clipboard) navigator.clipboard.writeText(v);
            });
        });
    </script>
@endsection
