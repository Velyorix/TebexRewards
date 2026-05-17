<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Azuriom\Plugin\Tebexrewards\Support\TebexCredentials;

class PaymentSyncService
{
    private const MAX_PAGES_FULL = 40;

    private const MAX_PAGES_INCREMENTAL = 2;

    private const EMPTY_PAGE_STOP_STREAK = 3;

    public function __construct(
        private TebexPluginApiService $pluginApi,
        private WebhookIngestionService $ingestion,
    ) {
    }

    /**
     * Import payments from Tebex Plugin API when not already stored.
     *
     * @return array{created: int, updated: int, ignored: int, pages: int, fetched: int, skipped_missing_key: bool, error: ?string}
     */
    public function sync(bool $fullImport = false): array
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'ignored' => 0,
            'pages' => 0,
            'fetched' => 0,
            'skipped_missing_key' => false,
            'error' => null,
        ];

        if (! TebexCredentials::hasPrivateKey()) {
            $stats['skipped_missing_key'] = true;

            return $stats;
        }

        try {
            if ($fullImport) {
                $this->syncPaginated($stats, self::MAX_PAGES_FULL);
            } else {
                $this->syncLatest($stats);
            }
        } catch (\Throwable $e) {
            $stats['error'] = $e->getMessage();
        }

        return $stats;
    }

    /**
     * @param  array{created: int, updated: int, ignored: int, pages: int, fetched: int, skipped_missing_key: bool, error: ?string}  $stats
     */
    private function syncLatest(array &$stats): void
    {
        $payments = $this->pluginApi->fetchLatestPayments(100);
        $stats['fetched'] += count($payments);
        $this->ingestList($payments, $stats);
        $stats['pages'] = 1;
    }

    /**
     * @param  array{created: int, updated: int, ignored: int, pages: int, fetched: int, skipped_missing_key: bool, error: ?string}  $stats
     */
    private function syncPaginated(array &$stats, int $maxPages): void
    {
        $emptyStreak = 0;

        for ($page = 1; $page <= $maxPages; $page++) {
            $payload = $this->pluginApi->fetchPaymentsPage($page);
            $payments = $payload['data'];
            if ($payments === []) {
                break;
            }

            $stats['pages']++;
            $stats['fetched'] += count($payments);

            $beforeCreated = $stats['created'];
            $this->ingestList($payments, $stats);

            if ($stats['created'] === $beforeCreated) {
                $emptyStreak++;
                if ($emptyStreak >= self::EMPTY_PAGE_STOP_STREAK) {
                    break;
                }
            } else {
                $emptyStreak = 0;
            }

            if ($page >= ($payload['last_page'] ?? $page)) {
                break;
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $payments
     * @param  array{created: int, updated: int, ignored: int, pages: int, fetched: int, skipped_missing_key: bool, error: ?string}  $stats
     */
    private function ingestList(array $payments, array &$stats): void
    {
        foreach ($payments as $payment) {
            $result = $this->ingestion->ingestPluginPayment($payment);
            $stats['created'] += (int) ($result['created'] ?? 0);
            $stats['updated'] += (int) ($result['updated'] ?? 0);
            $stats['ignored'] += (int) ($result['ignored'] ?? 0);
        }
    }
}
