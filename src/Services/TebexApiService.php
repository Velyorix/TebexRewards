<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Azuriom\Plugin\Tebexrewards\Support\TebexCredentials;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TebexApiService
{
    private const BASE_URL = 'https://headless.tebex.io/api';

    private ?string $cachedToken = null;

    public function getToken(): string
    {
        if ($this->cachedToken !== null) {
            return $this->cachedToken;
        }

        $token = $this->normalizePublicToken(TebexCredentials::publicToken());

        if ($token === '') {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.missing_api_key'));
        }

        $hint = $this->tokenFormatHint($token);
        if ($hint !== null) {
            throw new \RuntimeException($hint);
        }

        return $this->cachedToken = $token;
    }

    public function hasPrivateKey(): bool
    {
        return TebexCredentials::hasPrivateKey();
    }

    /**
     * @return array<string, mixed>
     */
    public function validatePublicToken(?string $rawToken = null): array
    {
        $token = $rawToken !== null && $rawToken !== ''
            ? $this->normalizePublicToken($rawToken)
            : $this->getToken();

        if ($token === '') {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.missing_api_key'));
        }

        $hint = $this->tokenFormatHint($token);
        if ($hint !== null) {
            throw new \RuntimeException($hint);
        }

        $response = $this->http(authenticated: false)->get($this->accountUrl($token));

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    /**
     * Optional: verifies Private Key via an authenticated Headless request.
     *
     * @return array{ok: bool, detail: string}
     */
    public function validatePrivateKey(
        ?string $rawPrivateKey = null,
        ?string $rawPublicToken = null,
        ?string $rawProjectId = null
    ): array {
        $privateKey = $rawPrivateKey !== null && $rawPrivateKey !== ''
            ? trim($rawPrivateKey)
            : TebexCredentials::privateKey();

        if ($privateKey === '') {
            return ['ok' => false, 'detail' => trans('tebexrewards::messages.admin.credentials.private_not_set')];
        }

        $token = $rawPublicToken !== null && $rawPublicToken !== ''
            ? $this->normalizePublicToken($rawPublicToken)
            : TebexCredentials::publicToken();

        if ($token === '') {
            return ['ok' => false, 'detail' => trans('tebexrewards::messages.errors.missing_api_key')];
        }

        $username = $rawProjectId !== null && trim($rawProjectId) !== ''
            ? trim($rawProjectId)
            : TebexCredentials::basicAuthUsername();

        $response = Http::withOptions([
            'verify' => app()->environment('production'),
            'timeout' => 20,
        ])
            ->withBasicAuth($username, $privateKey)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Azuriom/TebexRewards',
            ])
            ->get(self::BASE_URL.'/accounts/'.rawurlencode($token).'/packages', ['limit' => 1]);

        if ($response->successful()) {
            return ['ok' => true, 'detail' => trans('tebexrewards::messages.admin.credentials.private_ok')];
        }

        if (in_array($response->status(), [401, 403], true)) {
            return ['ok' => false, 'detail' => trans('tebexrewards::messages.admin.credentials.private_auth_failed')];
        }

        return ['ok' => true, 'detail' => trans('tebexrewards::messages.admin.credentials.private_skipped', [
            'status' => (string) $response->status(),
        ])];
    }

    public function http(bool $authenticated = false): PendingRequest
    {
        $request = Http::withOptions([
            'verify' => app()->environment('production'),
            'timeout' => 20,
        ])->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'Azuriom/TebexRewards',
        ]);

        if ($authenticated && TebexCredentials::hasPrivateKey()) {
            $request = $request->withBasicAuth(
                TebexCredentials::basicAuthUsername(),
                TebexCredentials::privateKey()
            );
        }

        return $request;
    }

    public function getAccount(): array
    {
        return $this->validatePublicToken();
    }

    public function getCategories(bool $includePackages = false): array
    {
        $token = $this->getToken();
        $query = $includePackages ? ['includePackages' => '1'] : [];
        $response = $this->http($this->hasPrivateKey())->get(
            self::BASE_URL.'/accounts/'.rawurlencode($token).'/categories',
            $query
        );

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    public function getPackages(): array
    {
        $token = $this->getToken();
        $response = $this->http($this->hasPrivateKey())->get(
            self::BASE_URL.'/accounts/'.rawurlencode($token).'/packages'
        );

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    public function getSidebarModules(): array
    {
        $token = $this->getToken();
        $response = $this->http($this->hasPrivateKey())->get(
            self::BASE_URL.'/accounts/'.rawurlencode($token).'/sidebar'
        );

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    private function accountUrl(string $token): string
    {
        return self::BASE_URL.'/accounts/'.rawurlencode($token);
    }

    private function normalizePublicToken(string $token): string
    {
        return trim(trim($token), " \t\n\r\0\x0B\"'");
    }

    private function tokenFormatHint(string $token): ?string
    {
        if (str_starts_with(strtolower($token), 'whsec_') || str_contains(strtolower($token), 'webhook')) {
            return trans('tebexrewards::messages.errors.token_looks_like_webhook_secret');
        }

        if (strlen($token) > 80 && preg_match('/^[a-f0-9]+$/i', $token)) {
            return trans('tebexrewards::messages.errors.token_looks_like_private_key');
        }

        return null;
    }

    private function throwIfFailed(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $detail = $response->json('message')
            ?? $response->json('error')
            ?? (is_array($response->json('errors')) ? json_encode($response->json('errors')) : null)
            ?? $response->body();

        if (is_array($detail)) {
            $detail = json_encode($detail, JSON_UNESCAPED_UNICODE);
        }

        $detail = mb_substr(trim((string) $detail), 0, 300);

        $messageKey = match ($response->status()) {
            401, 403 => 'errors.api_auth_failed',
            404 => 'errors.api_store_not_found',
            422 => 'errors.api_invalid_token',
            default => 'errors.api_request_failed',
        };

        throw new \RuntimeException(trans('tebexrewards::messages.'.$messageKey, [
            'status' => (string) $response->status(),
            'detail' => $detail !== '' ? $detail : '—',
        ]));
    }
}
