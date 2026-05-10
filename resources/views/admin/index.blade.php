@extends('admin.layouts.admin')

@section('title', trans('tebexrewards::messages.admin.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <p class="mb-0">{{ trans('tebexrewards::messages.admin.welcome') }}</p>
        </div>
    </div>
@endsection
