@extends('layouts.app')

@section('title', trans('tebexrewards::messages.leaderboard.page_standalone_title'))

@section('content')
    <h1 class="mb-2">{{ trans('tebexrewards::messages.leaderboard.page_standalone_title') }}</h1>
    <p class="text-muted mb-4">
        <a href="{{ route('tebexrewards.index') }}">{{ trans('tebexrewards::messages.leaderboard.back_to_hub') }}</a>
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
