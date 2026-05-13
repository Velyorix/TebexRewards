<?php

namespace Azuriom\Plugin\Tebexrewards\Http\View\Composers;

use Azuriom\Models\User;
use Azuriom\Plugin\Tebexrewards\Services\DonorRankService;
use Illuminate\View\View;

final class TebexrewardsProfileComposer
{
    public function __construct(private DonorRankService $donorRanks)
    {
    }

    public function compose(View $view): void
    {
        $cards = $this->existingCards($view);

        if ((bool) setting('tebexrewards.maintenance', false)) {
            $view->with('cards', $cards);

            return;
        }

        if (! (bool) setting('tebexrewards.ranks.profile_card_enabled', true)) {
            $view->with('cards', $cards);

            return;
        }

        $user = $view->offsetExists('user') ? $view->offsetGet('user') : null;
        if (! $user instanceof User) {
            $view->with('cards', $cards);

            return;
        }

        $total = $this->donorRanks->totalCompletedForUser($user);
        $tier = $this->donorRanks->bestTierForUser($user);
        $currency = (string) setting('tebexrewards.goal.currency', '€');

        $cards[] = [
            'name' => trans('tebexrewards::messages.profile.card_title'),
            'view' => 'tebexrewards::user.profile_card',
            'data' => [
                'totalFormatted' => number_format($total, 2, '.', ' '),
                'currency' => $currency,
                'tier' => $tier,
            ],
        ];

        $view->with('cards', $cards);
    }

    /**
     * @return list<array{name: string, view: string, data?: array}>
     */
    private function existingCards(View $view): array
    {
        if (! $view->offsetExists('cards')) {
            return [];
        }

        $cards = $view->offsetGet('cards');

        return is_array($cards) ? $cards : [];
    }
}
