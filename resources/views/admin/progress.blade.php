@extends('admin.layouts.admin')

@section('title', trans('tebexrewards::messages.admin.nav.progress'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">{{ trans('tebexrewards::messages.admin.progress.title') }}</h1>
            <p class="text-muted mb-0">{{ trans('tebexrewards::messages.admin.progress.intro') }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ $settingsUrl }}" class="btn btn-primary btn-sm">
                <i class="bi bi-gear"></i> {{ trans('tebexrewards::messages.admin.progress.open_settings') }}
            </a>
            <a href="{{ $hubUrl }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right"></i> {{ trans('tebexrewards::messages.admin.progress.view_public') }}
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header fw-semibold">{{ trans('tebexrewards::messages.admin.progress.preview_title') }}</div>
        <div class="card-body">
            @include('tebexrewards::public._progress', ['goal' => $goal])
        </div>
        <div class="card-footer small text-muted">
            {{ trans('tebexrewards::messages.admin.progress.preview_footer') }}
        </div>
    </div>
@endsection
