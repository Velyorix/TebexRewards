@if(($goal['enabled'] ?? false) && ($goal['target'] ?? 0) > 0)
    @php
        $percent = (int) ($goal['percent'] ?? 0);
        $current = (float) ($goal['current'] ?? 0);
        $target = (float) ($goal['target'] ?? 0);
        $currency = (string) ($goal['currency'] ?? '');
    @endphp

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h5 mb-0">{{ trans('tebexrewards::messages.goal.title') }}</h2>
                <span class="text-muted">
                    {{ number_format($current, 2, '.', ' ') }} {{ $currency }}
                    /
                    {{ number_format($target, 2, '.', ' ') }} {{ $currency }}
                </span>
            </div>

            <div class="progress" style="height: 14px;">
                <div
                    class="progress-bar"
                    role="progressbar"
                    style="width: {{ $percent }}%; background: linear-gradient(90deg, {{ $goal['color_start'] ?? '#00c853' }}, {{ $goal['color_mid'] ?? '#ffd600' }}, {{ $goal['color_end'] ?? '#ff3d00' }});"
                    aria-valuenow="{{ $percent }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>

            <div class="d-flex justify-content-between mt-2">
                <span class="text-muted">{{ trans('tebexrewards::messages.goal.percent', ['percent' => $percent]) }}</span>
                @if(($goal['reached'] ?? false) === true)
                    <span class="fw-semibold">{{ $goal['message_reached'] ?? '' }}</span>
                @endif
            </div>
        </div>
    </div>
@endif

