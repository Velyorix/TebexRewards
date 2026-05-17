<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Azuriom\Plugin\Tebexrewards\Support\TebexCredentials;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TebexPluginApiService
{
    private const BASE_URL = 'https://plugin.tebex.io';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchLatestPayments(int $limit = 100): array
    {
        $limit = max(1, min(100, $limit));
        $response = $this->http()->get(self::BASE_URL.'/payments', ['limit' => $limit]);

        $this->throwIfFailed($response);

        $json = $response->json();

        return is_array($json) && array_is_list($json) ? $json : [];
    }

    /**
     * @return array{data: array<int, array<string, mixed>>, current_page: int, last_page: int}
     */
    public function fetchPaymentsPage(int $page): array
    {
        $page = max(1, $page);
        $response = $this->http()->get(self::BASE_URL.'/payments', [
            'paged' => 1,
            'page' => $page,
        ]);

        $this->throwIfFailed($response);

        $json = $response->json();
        if (! is_array($json)) {
            return ['data' => [], 'current_page' => $page, 'last_page' => $page];
        }

        $data = $json['data'] ?? [];
        if (! is_array($data)) {
            $data = [];
        }

        return [
            'data' => array_values(array_filter($data, 'is_array')),
            'current_page' => (int) ($json['current_page'] ?? $page),
            'last_page' => (int) ($json['last_page'] ?? $page),
        ];
    }

    public function http(): PendingRequest
    {
        $secret = TebexCredentials::privateKey();
        if ($secret === '') {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.missing_private_key_for_payments'));
        }

        return Http::withOptions([
            'verify' => app()->environment('production'),
            'timeout' => 30,
        ])->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Tebex-Secret' => $secret,
            'User-Agent' => 'Azuriom/TebexRewards',
        ]);
    }

    private function throwIfFailed(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $detail = $response->json('message')
            ?? $response->json('error')
            ?? $response->body();

        if (is_array($detail)) {
            $detail = json_encode($detail, JSON_UNESCAPED_UNICODE);
        }

        $detail = mb_substr(trim((string) $detail), 0, 300);

        throw new \RuntimeException(trans('tebexrewards::messages.errors.plugin_api_request_failed', [
            'status' => (string) $response->status(),
            'detail' => $detail !== '' ? $detail : '—',
        ]));
    }
}
