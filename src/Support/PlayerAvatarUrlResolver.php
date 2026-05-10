<?php

namespace Azuriom\Plugin\Tebexrewards\Support;

use Azuriom\Games\Steam\SteamGame;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class PlayerAvatarUrlResolver
{

    public static function resolve(string $playerName, ?string $playerUuid = null, int $size = 64): string {
        $game = game();
        $name = trim($playerName) !== '' ? trim($playerName) : 'player';

        if (str_contains($name, '@') && filter_var($name, FILTER_VALIDATE_EMAIL)) {
            return asset('svg/user.svg');
        }

        if ($game instanceof SteamGame) {
            return self::steamAvatar($name, $playerUuid, $size);
        }

        return match ($game->id()) {
            'mc-offline' => self::minecraftByName($name, $size),
            'mc-online' => self::minecraftOnline($name, $playerUuid, $size),
            'mc-bedrock' => asset('svg/user.svg'),
            'hytale' => self::hytaleAvatar($name, $size),
            'fivem-cfx' => asset('svg/user.svg'),
            'none' => self::gravatarFallback($name, $size),
            default => self::defaultFallback($name, $size),
        };
    }

    private static function minecraftByName(string $name, int $size): string {
        return 'https://mc-heads.net/avatar/'.rawurlencode($name)."/{$size}.png";
    }

    private static function minecraftOnline(string $name, ?string $playerUuid, int $size): string {
        $uuid = self::normalizeMinecraftUuid($playerUuid);
        if ($uuid !== null) {
            return 'https://mc-heads.net/avatar/'.$uuid."/{$size}.png";
        }

        return self::minecraftByName($name, $size);
    }

    private static function normalizeMinecraftUuid(?string $uuid): ?string {
        if ($uuid === null || $uuid === '') {
            return null;
        }

        $hex = preg_replace('/[^a-fA-F0-9]/', '', $uuid);
        if (strlen($hex) !== 32) {
            return null;
        }

        return strtolower(
            substr($hex, 0, 8).'-'.
            substr($hex, 8, 4).'-'.
            substr($hex, 12, 4).'-'.
            substr($hex, 16, 4).'-'.
            substr($hex, 20, 12)
        );
    }

    private static function hytaleAvatar(string $name, int $size): string {
        return 'https://crafthead.net/hytale/avatar/'.rawurlencode($name)."/{$size}.png";
    }

    private static function steamAvatar(string $playerName, ?string $playerUuid, int $size): string {
        $steamId = null;
        if ($playerUuid !== null && preg_match('/^\d{17}$/', trim($playerUuid))) {
            $steamId = trim($playerUuid);
        } elseif (preg_match('/^\d{17}$/', trim($playerName))) {
            $steamId = trim($playerName);
        }

        $apiKey = config('services.steam.client_secret');
        if ($steamId === null || empty($apiKey)) {
            return asset('svg/user.svg');
        }

        $field = $size > 64 ? 'avatarfull' : ($size > 32 ? 'avatarmedium' : 'avatar');

        return Cache::remember(
            "tebexrewards.avatar.steam.{$steamId}.{$field}",
            now()->addMinutes(30),
            function () use ($steamId, $apiKey, $field) {
                $response = Http::get(SteamGame::USER_INFO_URL, [
                    'key' => $apiKey,
                    'steamids' => $steamId,
                ]);

                if (! $response->successful()) {
                    return asset('svg/user.svg');
                }

                $player = $response->json('response.players.0');

                return Arr::get($player, $field, asset('svg/user.svg'));
            }
        );
    }

    private static function gravatarFallback(string $name, int $size): string {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($name)).'?d=mp&s='.$size;
    }

    private static function defaultFallback(string $name, int $size): string {
        return self::gravatarFallback($name, $size);
    }
}
