<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class SyncService
{
    public function __construct(
        private TebexApiService $api,
        private PaymentSyncService $payments,
    ) {
    }

    /**
     *
     * @return array{
     *     account: bool,
     *     categories: int,
     *     packages: int,
     *     sidebar: bool,
     *     synced: int,
     *     skipped: int,
     *     account_name: ?string,
     *     payments: array{created: int, updated: int, ignored: int, pages: int, fetched: int, skipped_missing_key: bool, error: ?string}
     * }
     */
    public function sync(bool $force = false): array
    {
        if ((bool) setting('tebexrewards.maintenance', false)) {
            throw new \RuntimeException(trans('tebexrewards::messages.errors.sync_maintenance'));
        }

        $ttl = 3600;

        if ($force) {
            Cache::forget('tebexrewards.headless.account');
            Cache::forget('tebexrewards.headless.categories');
            Cache::forget('tebexrewards.headless.packages');
            Cache::forget('tebexrewards.headless.sidebar');
        }

        $account = Cache::remember('tebexrewards.headless.account', $ttl, fn () => $this->api->getAccount());
        $categoriesPayload = Cache::remember('tebexrewards.headless.categories', $ttl, fn () => $this->api->getCategories(true));
        $packagesPayload = Cache::remember('tebexrewards.headless.packages', $ttl, fn () => $this->api->getPackages());
        $sidebarLoaded = false;
        try {
            Cache::remember('tebexrewards.headless.sidebar', $ttl, function () use (&$sidebarLoaded) {
                $this->api->getSidebarModules();
                $sidebarLoaded = true;

                return ['ok' => true];
            });
        } catch (\Throwable $e) {
            report($e);
        }

        $categoryCount = $this->countListItems($categoriesPayload, 'data');
        $packageCount = $this->countListItems($packagesPayload, 'data');

        $accountName = is_array($account)
            ? (Arr::get($account, 'data.name') ?? Arr::get($account, 'name'))
            : null;
        $accountName = is_string($accountName) ? $accountName : null;

        $synced = 1 + $categoryCount + $packageCount + ($sidebarLoaded ? 1 : 0);

        $paymentStats = [
            'created' => 0,
            'updated' => 0,
            'ignored' => 0,
            'pages' => 0,
            'fetched' => 0,
            'skipped_missing_key' => false,
            'error' => null,
        ];

        try {
            $paymentStats = $this->payments->sync($force);
        } catch (\Throwable $e) {
            report($e);
            $paymentStats['error'] = $e->getMessage();
        }

        return [
            'account' => true,
            'categories' => $categoryCount,
            'packages' => $packageCount,
            'sidebar' => $sidebarLoaded,
            'synced' => $synced,
            'skipped' => 0,
            'account_name' => $accountName,
            'payments' => $paymentStats,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function countListItems(array $payload, string $dataKey = 'data'): int
    {
        $items = Arr::get($payload, $dataKey);
        if (is_array($items)) {
            return count($items);
        }

        return is_array($payload) ? count($payload) : 0;
    }
}
