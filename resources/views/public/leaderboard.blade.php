@extends('layouts.app')

@section('title', trans('tebexrewards::messages.hub.title'))

@section('content')
    <h1 class="mb-4">{{ trans('tebexrewards::messages.hub.title') }}</h1>
    <p class="text-muted mb-4">{{ trans('tebexrewards::messages.hub.intro') }}</p>

    @include('tebexrewards::public._progress', ['goal' => $goal])

    @include('tebexrewards::public._last_purchaser')

    <h2 class="h4 mb-3">{{ trans('tebexrewards::messages.leaderboard.title') }}</h2>
    <p class="small mb-3">
        <a href="{{ route('tebexrewards.leaderboard') }}">{{ trans('tebexrewards::messages.hub.leaderboard_standalone_cta') }}</a>
        <span class="text-muted"> — {{ trans('tebexrewards::messages.hub.leaderboard_standalone_hint') }}</span>
    </p>

    @include('tebexrewards::public._leaderboard_panel', [
        'formActionRoute' => $formActionRoute,
        'period' => $period,
        'limit' => $limit,
        'columns' => $columns,
        'showMedals' => $showMedals,
        'showAvatars' => $showAvatars,
        'entries' => $entries,
        'pollSeconds' => $pollSeconds,
        'tableDensity' => $tableDensity,
    ])
@endsection
