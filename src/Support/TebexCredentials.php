<?php

namespace Azuriom\Plugin\Tebexrewards\Support;

use Illuminate\Support\Facades\Crypt;

final class TebexCredentials
{
    public const SETTING_PUBLIC_TOKEN = 'tebexrewards.headless.public_token';

    public const SETTING_PRIVATE_KEY = 'tebexrewards.headless.private_key';

    public const SETTING_PROJECT_ID = 'tebexrewards.headless.project_id';

    /** @deprecated Use SETTING_PUBLIC_TOKEN; kept for upgrades from older versions. */
    public const SETTING_PUBLIC_TOKEN_LEGACY = 'tebexrewards.tebex_api_key';

    public const SETTING_WEBHOOK_SECRET = 'tebexrewards.webhook_secret';

    public static function decrypt(string $settingKey): string
    {
        $encrypted = (string) setting($settingKey, '');
        if ($encrypted === '') {
            return '';
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable) {
            return '';
        }
    }

    public static function encrypt(string $plain): string
    {
        $plain = trim($plain);

        return $plain === '' ? '' : Crypt::encryptString($plain);
    }

    public static function publicToken(): string
    {
        $token = self::decrypt(self::SETTING_PUBLIC_TOKEN);
        if ($token === '') {
            $token = self::decrypt(self::SETTING_PUBLIC_TOKEN_LEGACY);
        }
        if ($token === '' && plugins()->isEnabled('tebex')) {
            $token = (string) setting('tebex.key', '');
        }

        return trim($token, " \t\n\r\0\x0B\"'");
    }

    public static function privateKey(): string
    {
        $key = self::decrypt(self::SETTING_PRIVATE_KEY);
        if ($key === '' && plugins()->isEnabled('tebex')) {
            $key = self::decrypt('tebex.private_key');
        }

        return trim($key);
    }

    public static function projectId(): string
    {
        $id = trim((string) setting(self::SETTING_PROJECT_ID, ''));
        if ($id === '' && plugins()->isEnabled('tebex')) {
            $id = trim((string) setting('tebex.project_id', ''));
        }

        return $id;
    }

    public static function basicAuthUsername(): string
    {
        $projectId = self::projectId();

        return $projectId !== '' ? $projectId : self::publicToken();
    }

    public static function hasPublicToken(): bool
    {
        return self::publicToken() !== '';
    }

    public static function hasPrivateKey(): bool
    {
        return self::privateKey() !== '';
    }

    public static function hasWebhookSecret(): bool
    {
        return self::decrypt(self::SETTING_WEBHOOK_SECRET) !== '';
    }

    /**
     * @return array{public_token: string, private_key: string, project_id: string}
     */
    public static function fromTebexPlugin(): array
    {
        if (! plugins()->isEnabled('tebex')) {
            return ['public_token' => '', 'private_key' => '', 'project_id' => ''];
        }

        return [
            'public_token' => (string) setting('tebex.key', ''),
            'private_key' => self::decrypt('tebex.private_key'),
            'project_id' => (string) setting('tebex.project_id', ''),
        ];
    }
}
