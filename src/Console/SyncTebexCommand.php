<?php

namespace Azuriom\Plugin\Tebexrewards\Console;

use Azuriom\Plugin\Tebexrewards\Services\SyncService;
use Illuminate\Console\Command;

class SyncTebexCommand extends Command
{
    protected $signature = 'tebexrewards:sync {--force : Bypass cache and force remote fetch}';

    protected $description = 'Warm Tebex Headless API cache and validate credentials';

    public function handle(SyncService $sync): int
    {
        try {
            $result = $sync->sync((bool) $this->option('force'));
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            report($e);

            return self::FAILURE;
        }

        $this->info(trans('tebexrewards::messages.console.sync_done', [
            'synced' => $result['synced'],
            'skipped' => $result['skipped'],
        ]));

        return self::SUCCESS;
    }
}

