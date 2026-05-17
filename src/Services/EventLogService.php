<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Azuriom\Plugin\Tebexrewards\Models\EventLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EventLogService
{
    private const MAX_ROWS = 500;

    public function isAvailable(): bool
    {
        return Schema::hasTable('tebex_event_logs');
    }

    public function log(
        string $channel,
        string $event,
        string $message,
        string $level = EventLog::LEVEL_INFO,
        ?array $context = null,
        ?Request $request = null,
    ): ?EventLog {
        $message = mb_substr($message, 0, 500);
        $context = $this->sanitizeContext($context);

        if (! $this->isAvailable()) {
            Log::info('TebexRewards event', [
                'channel' => $channel,
                'event' => $event,
                'level' => $level,
                'message' => $message,
                'context' => $context,
                'ip' => $request?->ip(),
            ]);

            return null;
        }

        try {
            $entry = EventLog::query()->create([
                'channel' => $channel,
                'level' => $level,
                'event' => $event,
                'message' => $message,
                'context' => $context,
                'ip_address' => $request?->ip(),
                'created_at' => now(),
            ]);

            $this->pruneOldEntries();

            return $entry;
        } catch (\Throwable $e) {
            report($e);
            Log::warning('TebexRewards: could not write event log', [
                'channel' => $channel,
                'event' => $event,
                'message' => $message,
            ]);

            return null;
        }
    }

    public function paginate(int $perPage = 25): LengthAwarePaginator
    {
        if (! $this->isAvailable()) {
            return new Paginator([], 0, $perPage, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        return EventLog::query()
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return array{webhook_24h: int, webhook_ok_24h: int, last_webhook_at: mixed, headless_24h: int, transactions_24h: int}
     */
    public function dashboardStats(): array
    {
        $since = now()->subDay();

        if (! $this->isAvailable()) {
            return [
                'webhook_24h' => 0,
                'webhook_ok_24h' => 0,
                'last_webhook_at' => null,
                'headless_24h' => 0,
                'transactions_24h' => $this->countRecentTransactions($since),
            ];
        }

        $webhook24h = EventLog::query()->webhook()->where('created_at', '>=', $since)->count();
        $webhookOk24h = EventLog::query()->webhook()
            ->where('created_at', '>=', $since)
            ->where('level', EventLog::LEVEL_INFO)
            ->whereIn('event', ['payment.created', 'payment.updated', 'validation.ok'])
            ->count();

        $lastWebhook = EventLog::query()->webhook()->orderByDesc('id')->first()?->created_at;

        $headless24h = EventLog::query()->headless()->where('created_at', '>=', $since)->count();

        return [
            'webhook_24h' => $webhook24h,
            'webhook_ok_24h' => $webhookOk24h,
            'last_webhook_at' => $lastWebhook,
            'headless_24h' => $headless24h,
            'transactions_24h' => $this->countRecentTransactions($since),
        ];
    }

    public function clearAll(): int
    {
        if (! $this->isAvailable()) {
            return 0;
        }

        return EventLog::query()->delete();
    }

    /**
     * @param  array<string, mixed>|null  $context
     * @return array<string, mixed>|null
     */
    private function sanitizeContext(?array $context): ?array
    {
        if ($context === null || $context === []) {
            return null;
        }

        $json = json_encode($context, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return ['summary' => 'context_unserializable'];
        }

        if (strlen($json) > 4000) {
            return [
                'summary' => 'payload_truncated',
                'preview' => mb_substr($json, 0, 4000),
            ];
        }

        return $context;
    }

    private function pruneOldEntries(): void
    {
        if (! $this->isAvailable()) {
            return;
        }

        $count = EventLog::query()->count();
        if ($count <= self::MAX_ROWS) {
            return;
        }

        $thresholdId = EventLog::query()
            ->orderByDesc('id')
            ->skip(self::MAX_ROWS)
            ->value('id');

        if ($thresholdId !== null) {
            EventLog::query()->where('id', '<=', $thresholdId)->delete();
        }
    }

    private function countRecentTransactions(\Illuminate\Support\Carbon $since): int
    {
        if (! Schema::hasTable('tebex_transactions')) {
            return 0;
        }

        return \Azuriom\Plugin\Tebexrewards\Models\Transaction::query()
            ->where('created_at', '>=', $since)
            ->count();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function webhookPayloadSummary(array $payload): array
    {
        $subject = Arr::get($payload, 'subject');
        $subject = is_array($subject) ? $subject : [];

        return array_filter([
            'type' => Arr::get($payload, 'type'),
            'transaction_id' => Arr::get($subject, 'transaction_id'),
            'status' => Arr::get($subject, 'status.description') ?? Arr::get($subject, 'status'),
            'player' => Arr::get($subject, 'customer.username') ?? Arr::get($subject, 'username'),
            'amount' => Arr::get($subject, 'price_paid.amount') ?? Arr::get($subject, 'price.amount'),
            'currency' => Arr::get($subject, 'price_paid.currency') ?? Arr::get($subject, 'price.currency'),
            'package' => Arr::get($subject, 'products.0.name'),
        ], fn ($v) => $v !== null && $v !== '');
    }
}
