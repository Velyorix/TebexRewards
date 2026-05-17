@if(isset($user) && $user instanceof \Azuriom\Models\User)
    @if(!(bool) setting('tebexrewards.maintenance', false) && (bool) setting('tebexrewards.ranks.profile_card_enabled', true))
        @php
            $svc = app(\Azuriom\Plugin\Tebexrewards\Services\DonorRankService::class);
            $totalFormatted = number_format($svc->totalCompletedForUser($user), 2, '.', ' ');
            $currency = (string) setting('tebexrewards.goal.currency', '€');
            $tier = $svc->bestTierForUser($user);
        @endphp
        <div class="card border shadow-sm mb-4 tebexrewards-profile-snippet">
            <div class="card-body">
                <h2 class="h6 card-title">{{ trans('tebexrewards::messages.profile.card_title') }}</h2>
                @include('tebexrewards::user.profile_card', compact('totalFormatted', 'currency', 'tier'))
            </div>
        </div>
    @endif
@endif
