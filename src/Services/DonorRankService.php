<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Azuriom\Models\User;
use Azuriom\Plugin\Tebexrewards\Models\RankTier;
use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;

final class DonorRankService {

    public function totalCompletedForUser(User $user): float {

        $sum = Transaction::query()
            ->completed()
            ->where(function (Builder $q) use ($user) {
                $q->where('player_name', $user->name);

                $gid = $user->game_id;
                if (! is_string($gid) || $gid === '') {
                    return;
                }

                $q->orWhere('player_uuid', $gid);

                $compact = preg_replace('/[^a-fA-F0-9]/', '', $gid);
                if (strlen($compact) === 32) {
                    $dashed = sprintf(
                        '%s-%s-%s-%s-%s',
                        substr($compact, 0, 8),
                        substr($compact, 8, 4),
                        substr($compact, 12, 4),
                        substr($compact, 16, 4),
                        substr($compact, 20, 12)
                    );
                    $q->orWhere('player_uuid', $compact)->orWhere('player_uuid', $dashed);
                }
            })
            ->sum('amount');

        return (float) ($sum ?? 0);
    }

    public function bestTierForUser(User $user): ?RankTier {

        return RankTier::bestForAmount($this->totalCompletedForUser($user));
    }
}
