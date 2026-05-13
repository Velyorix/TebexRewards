<?php

use Azuriom\Models\User;
use Azuriom\Plugin\Tebexrewards\Support\PlayerAvatarUrlResolver;

if (! function_exists('tebexrewards_avatar_url')) {
    /**
     * @param  string  $playerName  Primary identifier shown on the leaderboard.
     * @param  string|null  $playerUuid  Optional identifier stored by Tebex (e.g. Minecraft UUID, Steam64) — used to match Azuriom {@see User::$game_id}.
     */
    function tebexrewards_avatar_url(string $playerName, ?string $playerUuid = null, int $size = 64): string {
        if (function_exists('tebex_get_avatar')) {
            $user = User::query()->where('name', $playerName)->first();

            if ($user === null && $playerUuid !== null && $playerUuid !== '') {
                $user = User::query()->where('game_id', $playerUuid)->first();

                if ($user === null) {
                    $compact = preg_replace('/[^a-fA-F0-9]/', '', $playerUuid);
                    if (strlen($compact) === 32) {
                        $dashed = sprintf(
                            '%s-%s-%s-%s-%s',
                            substr($compact, 0, 8),
                            substr($compact, 8, 4),
                            substr($compact, 12, 4),
                            substr($compact, 16, 4),
                            substr($compact, 20, 12)
                        );
                        $user = User::query()
                            ->where(function ($q) use ($compact, $dashed): void {
                                $q->where('game_id', $compact)->orWhere('game_id', $dashed);
                            })
                            ->first();
                    }
                }
            }

            if ($user !== null) {
                return tebex_get_avatar($user, $size);
            }

            return tebex_get_avatar($playerName, $size);
        }

        return PlayerAvatarUrlResolver::resolve($playerName, $playerUuid, $size);
    }
}
