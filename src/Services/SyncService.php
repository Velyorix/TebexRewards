<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Illuminate\Support\Facades\Cache;

class SyncService
{
    public function __construct(private TebexApiService $api)
    {
    }

    /**
     * @return array{synced:int, skipped:int}
     */
    public function sync(bool $force = false): array {
        if ((bool) setting('tebexrewards.maintenance', false)) {
            return ['synced' => 0, 'skipped' => 0];
        }

        $ttl = 3600;

        if ($force) {
            Cache::forget('tebexrewards.headless.account');
            Cache::forget('tebexrewards.headless.categories');
            Cache::forget('tebexrewards.headless.packages');
            Cache::forget('tebexrewards.headless.sidebar');
        }

        Cache::remember('tebexrewards.headless.account', $ttl, fn () => $this->api->getAccount());
        Cache::remember('tebexrewards.headless.categories', $ttl, fn () => $this->api->getCategories(true));
        Cache::remember('tebexrewards.headless.packages', $ttl, fn () => $this->api->getPackages());
        Cache::remember('tebexrewards.headless.sidebar', $ttl, fn () => $this->api->getSidebarModules());

        return ['synced' => 0, 'skipped' => 0];
    }
}

