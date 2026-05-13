@if($tier)
    <p class="mb-2">
        {{ trans('tebexrewards::messages.profile.total_label') }}:
        <strong>{{ $totalFormatted }} {{ $currency }}</strong>
    </p>
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if($tier->icon && filter_var($tier->icon, FILTER_VALIDATE_URL))
            <img src="{{ $tier->icon }}" alt="" width="40" height="40" class="rounded border" loading="lazy">
        @elseif($tier->icon)
            <span class="fs-2" aria-hidden="true">{{ $tier->icon }}</span>
        @endif
        @php($badgeColor = $tier->color_hex ?: '#6c757d')
        <span class="badge rounded-pill px-3 py-2 fs-6 border text-white" style="background-color: {{ $badgeColor }};">
            {{ $tier->rank_name }}
        </span>
    </div>
    <p class="small text-muted mt-2 mb-0">{{ trans('tebexrewards::messages.profile.tier_hint') }}</p>
@else
    <p class="mb-2">
        {{ trans('tebexrewards::messages.profile.total_label') }}:
        <strong>{{ $totalFormatted }} {{ $currency }}</strong>
    </p>
    <p class="text-muted mb-0">{{ trans('tebexrewards::messages.profile.no_tier') }}</p>
@endif
