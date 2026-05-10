@extends('layouts.app')

@section('title', trans('tebexrewards::messages.leaderboard.title'))

@section('content')
    <h1 class="mb-4">{{ trans('tebexrewards::messages.leaderboard.title') }}</h1>

    @include('tebexrewards::public._progress', ['goal' => $goal])

    @include('tebexrewards::public._last_purchaser')

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tebexrewards.index') }}" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="periodSelect">{{ trans('tebexrewards::messages.leaderboard.period') }}</label>
                    <select id="periodSelect" name="period" class="form-select">
                        @foreach(['all', 'month', 'week', 'day'] as $p)
                            <option value="{{ $p }}" @selected($period === $p)>{{ trans('tebexrewards::messages.period.'.$p) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="limitInput">{{ trans('tebexrewards::messages.leaderboard.limit') }}</label>
                    <input id="limitInput" class="form-control" type="number" min="5" max="100" step="1" name="limit" value="{{ $limit }}" disabled>
                    <small class="text-muted">{{ trans('tebexrewards::messages.leaderboard.limit_locked') }}</small>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> {{ trans('tebexrewards::messages.actions.apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card" id="tebexrewards-leaderboard-root"
         data-url="{{ route('tebexrewards.api.widgets.leaderboard') }}"
         data-period="{{ $period }}">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    @foreach($columns as $col)
                        <th scope="col">{{ trans('tebexrewards::messages.leaderboard.columns.'.$col) }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody id="tebexrewards-leaderboard-body">
                @forelse($entries as $entry)
                    <tr>
                        @foreach($columns as $col)
                            @if($col === 'rank')
                                <td>
                                    @php($r = (int) $entry['rank'])
                                    @if($showMedals && $r <= 3)
                                        <span class="me-1">
                                            @if($r === 1) 🥇 @elseif($r === 2) 🥈 @else 🥉 @endif
                                        </span>
                                    @endif
                                    {{ $r }}
                                </td>
                            @elseif($col === 'player')
                                <td class="d-flex align-items-center gap-2">
                                    @if($showAvatars)
                                        <img
                                            src="{{ tebexrewards_avatar_url($entry['player_name'], $entry['player_uuid'] ?? null, 24) }}"
                                            width="24"
                                            height="24"
                                            class="rounded"
                                            alt="{{ $entry['player_name'] }}"
                                            loading="lazy"
                                        >
                                    @endif
                                    <span>{{ $entry['player_name'] }}</span>
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
                        <td colspan="{{ count($columns) }}" class="text-center text-muted py-4">
                            {{ trans('tebexrewards::messages.leaderboard.empty') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (function () {
            const root = document.getElementById('tebexrewards-leaderboard-root');
            const tbody = document.getElementById('tebexrewards-leaderboard-body');
            if (!root || !tbody) return;

            const baseUrl = root.getAttribute('data-url');
            const emptyMessage = @json(trans('tebexrewards::messages.leaderboard.empty'));

            function medalEmoji(rank) {
                if (rank === 1) return '🥇 ';
                if (rank === 2) return '🥈 ';
                if (rank === 3) return '🥉 ';
                return '';
            }

            function render(json) {
                const cols = json.columns || [];
                const entries = json.entries || [];
                const showMedals = !!json.show_medals;
                const showAvatars = !!json.show_avatars;

                tbody.textContent = '';

                if (!entries.length) {
                    const tr = document.createElement('tr');
                    const td = document.createElement('td');
                    td.colSpan = Math.max(1, cols.length);
                    td.className = 'text-center text-muted py-4';
                    td.textContent = emptyMessage;
                    tr.appendChild(td);
                    tbody.appendChild(tr);
                    return;
                }

                entries.forEach(function (entry) {
                    const tr = document.createElement('tr');
                    cols.forEach(function (col) {
                        const td = document.createElement('td');
                        if (col === 'rank') {
                            const r = parseInt(entry.rank, 10) || 0;
                            if (showMedals && r <= 3) {
                                td.appendChild(document.createTextNode(medalEmoji(r)));
                            }
                            td.appendChild(document.createTextNode(String(r)));
                        } else if (col === 'player') {
                            td.className = 'd-flex align-items-center gap-2';
                            if (showAvatars && entry.avatar_url) {
                                const img = document.createElement('img');
                                img.src = entry.avatar_url;
                                img.width = 24;
                                img.height = 24;
                                img.className = 'rounded';
                                img.alt = entry.player_name || '';
                                img.loading = 'lazy';
                                td.appendChild(img);
                            }
                            const span = document.createElement('span');
                            span.textContent = entry.player_name || '';
                            td.appendChild(span);
                        } else if (col === 'amount') {
                            td.textContent = entry.amount_display != null ? String(entry.amount_display) : '';
                        } else if (col === 'purchases') {
                            td.textContent = String(entry.purchases_count != null ? entry.purchases_count : '');
                        }
                        tr.appendChild(td);
                    });
                    tbody.appendChild(tr);
                });
            }

            async function refresh() {
                try {
                    const period = root.getAttribute('data-period') || 'all';
                    const url = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'period=' + encodeURIComponent(period);
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const json = await res.json();
                    render(json);
                } catch (e) {}
            }

            refresh();
            setInterval(refresh, 10000);

            const periodSelect = document.getElementById('periodSelect');
            if (periodSelect) {
                periodSelect.addEventListener('change', function () {
                    root.setAttribute('data-period', periodSelect.value);
                    refresh();
                });
            }
        })();
    </script>
@endsection

