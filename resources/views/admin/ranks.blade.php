@extends('admin.layouts.admin')

@section('title', trans('tebexrewards::messages.admin.nav.ranks'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">{{ trans('tebexrewards::messages.admin.ranks.title') }}</h1>
            <p class="text-muted mb-0">{{ trans('tebexrewards::messages.admin.ranks.intro') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('tebexrewards.admin.settings') }}#pane-goal" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-piggy-bank"></i> {{ trans('tebexrewards::messages.admin.ranks.link_goal_settings') }}
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <h2 class="h5">{{ trans('tebexrewards::messages.admin.ranks.section_profile') }}</h2>
            <p class="text-muted small">{{ trans('tebexrewards::messages.admin.ranks.section_profile_help') }}</p>
            <form action="{{ route('tebexrewards.admin.ranks.profile_setting') }}" method="POST" class="row align-items-end gy-2">
                @csrf
                <div class="col-md-8">
                    <input type="hidden" name="ranks_profile_card_enabled" value="0">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="profileCardSwitch" name="ranks_profile_card_enabled" value="1"
                               @checked((bool) old('ranks_profile_card_enabled', $profile_card_enabled))>
                        <label class="form-check-label" for="profileCardSwitch">{{ trans('tebexrewards::messages.admin.ranks.profile_card_enabled') }}</label>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check-lg"></i> {{ trans('messages.actions.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">{{ $editing ? trans('tebexrewards::messages.admin.ranks.form_edit') : trans('tebexrewards::messages.admin.ranks.form_add') }}</span>
            @if($editing)
                <a href="{{ route('tebexrewards.admin.ranks') }}" class="btn btn-sm btn-outline-secondary">{{ trans('tebexrewards::messages.admin.ranks.cancel_edit') }}</a>
            @endif
        </div>
        <div class="card-body">
            <form method="POST" action="{{ $editing ? route('tebexrewards.admin.ranks.update', $editing) : route('tebexrewards.admin.ranks.store') }}">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="rankName">{{ trans('tebexrewards::messages.admin.ranks.fields.name') }}</label>
                        <input type="text" class="form-control @error('rank_name') is-invalid @enderror" id="rankName" name="rank_name" maxlength="64"
                               value="{{ old('rank_name', $editing?->rank_name) }}" required>
                        @error('rank_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="minAmount">{{ trans('tebexrewards::messages.admin.ranks.fields.min_amount') }}</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('min_amount') is-invalid @enderror" id="minAmount" name="min_amount"
                               value="{{ old('min_amount', $editing?->min_amount) }}" required>
                        @error('min_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="iconField">{{ trans('tebexrewards::messages.admin.ranks.fields.icon') }}</label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror" id="iconField" name="icon" maxlength="255"
                               value="{{ old('icon', $editing?->icon) }}" placeholder="🥇 ou URL">
                        @error('icon')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        <small class="text-muted">{{ trans('tebexrewards::messages.admin.ranks.icon_help') }}</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="colorHex">{{ trans('tebexrewards::messages.admin.ranks.fields.color_hex') }}</label>
                        <input type="text" class="form-control font-monospace @error('color_hex') is-invalid @enderror" id="colorHex" name="color_hex" pattern="#[0-9A-Fa-f]{6}"
                               value="{{ old('color_hex', $editing?->color_hex) }}" placeholder="#cd7f32">
                        @error('color_hex')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <input type="hidden" name="enabled" value="0">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="tierEnabled" name="enabled" value="1" @checked((bool) old('enabled', $editing?->enabled ?? true))>
                            <label class="form-check-label small" for="tierEnabled">{{ trans('tebexrewards::messages.admin.ranks.fields.enabled') }}</label>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> {{ $editing ? trans('messages.actions.update') : trans('messages.actions.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">{{ trans('tebexrewards::messages.admin.ranks.table_title') }}</span>
            <form action="{{ route('tebexrewards.admin.ranks.reset') }}" method="POST" onsubmit="return confirm(@json(trans('tebexrewards::messages.admin.ranks.reset_confirm')));">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-arrow-counterclockwise"></i> {{ trans('tebexrewards::messages.admin.ranks.reset_defaults') }}
                </button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>{{ trans('tebexrewards::messages.admin.ranks.fields.name') }}</th>
                    <th>{{ trans('tebexrewards::messages.admin.ranks.fields.min_amount') }}</th>
                    <th>{{ trans('tebexrewards::messages.admin.ranks.fields.icon') }}</th>
                    <th>{{ trans('tebexrewards::messages.admin.ranks.fields.color_hex') }}</th>
                    <th>{{ trans('tebexrewards::messages.admin.ranks.fields.enabled') }}</th>
                    <th class="text-end">{{ trans('tebexrewards::messages.admin.ranks.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($tiers as $tier)
                    <tr @class(['table-secondary' => $editing && $editing->is($tier)])>
                        <td class="fw-semibold">{{ $tier->rank_name }}</td>
                        <td>{{ number_format((float) $tier->min_amount, 2, '.', ' ') }}</td>
                        <td>
                            @if($tier->icon && filter_var($tier->icon, FILTER_VALIDATE_URL))
                                <img src="{{ $tier->icon }}" alt="" width="28" height="28" class="rounded border">
                            @else
                                <span class="fs-5">{{ $tier->icon }}</span>
                            @endif
                        </td>
                        <td>
                            @if($tier->color_hex)
                                <span class="badge rounded-pill border" style="background-color: {{ $tier->color_hex }}; min-width: 4rem;">&nbsp;</span>
                                <code class="small ms-1">{{ $tier->color_hex }}</code>
                            @endif
                        </td>
                        <td>@if($tier->enabled)<span class="text-success"><i class="bi bi-check-circle"></i></span>@else<span class="text-muted">—</span>@endif</td>
                        <td class="text-end">
                            <a href="{{ route('tebexrewards.admin.ranks.edit', $tier) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('tebexrewards.admin.ranks.destroy', $tier) }}" method="POST" class="d-inline" onsubmit="return confirm(@json(trans('tebexrewards::messages.admin.ranks.delete_confirm')));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">{{ trans('tebexrewards::messages.admin.ranks.empty') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
