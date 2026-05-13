<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class TebexApiService
{
    private const BASE_URL = 'https://headless.tebex.io/api';

    private ?string $cachedToken = null;

    public function getToken(): string {
        if ($this->cachedToken !== null) {
            return $this->cachedToken;
        }

        $encrypted = (string) setting('tebexrewards.tebex_api_key', '');

        if ($encrypted === '') {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.missing_api_key'));
        }

        try {
            $token = Crypt::decryptString($encrypted);
        } catch (\Throwable $e) {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.invalid_api_key'));
        }

        if ($token === '') {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.missing_api_key'));
        }

        return $this->cachedToken = $token;
    }

    public function http(): PendingRequest {
        return Http::withOptions([
            'verify' => app()->environment('production'),
            'timeout' => 20,
        ])->withHeaders([
            'Accept' => 'application/json',
            'User-Agent' => 'Azuriom/TebexRewards',
        ]);
    }

    public function getAccount(): array {
        $token = $this->getToken();

        $response = $this->http()->get(self::BASE_URL."/accounts/{$token}");

        if (! $response->successful()) {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.api_request_failed', [
                'status' => (string) $response->status(),
            ]));
        }

        return $response->json() ?? [];
    }

    public function getCategories(bool $includePackages = false): array {
        $token = $this->getToken();

        $query = $includePackages ? ['includePackages' => '1'] : [];
        $response = $this->http()->get(self::BASE_URL."/accounts/{$token}/categories", $query);

        if (! $response->successful()) {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.api_request_failed', [
                'status' => (string) $response->status(),
            ]));
        }

        return $response->json() ?? [];
    }

    public function getPackages(): array {
        $token = $this->getToken();

        $response = $this->http()->get(self::BASE_URL."/accounts/{$token}/packages");

        if (! $response->successful()) {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.api_request_failed', [
                'status' => (string) $response->status(),
            ]));
        }

        return $response->json() ?? [];
    }

    public function getSidebarModules(): array {
        $token = $this->getToken();

        $response = $this->http()->get(self::BASE_URL."/accounts/{$token}/sidebar");

        if (! $response->successful()) {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.api_request_failed', [
                'status' => (string) $response->status(),
            ]));
        }

        return $response->json() ?? [];
    }
}

