@extends('admin.layouts.admin')

@section('title', trans('tebexrewards::messages.admin.nav.ranks'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <p class="mb-0">{{ trans('tebexrewards::messages.admin.pages.ranks_hint') }}</p>
        </div>
    </div>
@endsection

