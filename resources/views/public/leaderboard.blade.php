@extends('layouts.app')

@section('title', trans('tebexrewards::messages.leaderboard.title'))

@section('content')
    <h1 class="mb-4">{{ trans('tebexrewards::messages.leaderboard.title') }}</h1>

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

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    @foreach($columns as $col)
                        <th scope="col">{{ trans('tebexrewards::messages.leaderboard.columns.'.$col) }}</th>
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
                                            src="{{ avatar_url($entry['player_name'], 24) }}"
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
@endsection

