<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class WebhookIngestionService
{
    /**
     * @return array{created:int, updated:int, ignored:int, transaction_id:?string}
     */
    public function ingest(array $payload): array {
        $subject = Arr::get($payload, 'subject');
        if (! is_array($subject)) {
            return ['created' => 0, 'updated' => 0, 'ignored' => 1, 'transaction_id' => null];
        }

        $transactionId = (string) (Arr::get($subject, 'transaction_id') ?? '');
        if ($transactionId === '') {
            return ['created' => 0, 'updated' => 0, 'ignored' => 1, 'transaction_id' => null];
        }

        $status = (string) (Arr::get($subject, 'status.description') ?? Arr::get($subject, 'status') ?? 'Complete');
        $normalizedStatus = $this->normalizeStatus($status);

        $purchaseDateRaw = Arr::get($subject, 'created_at') ?? Arr::get($payload, 'date') ?? null;
        try {
            $purchaseDate = $purchaseDateRaw ? Carbon::parse($purchaseDateRaw) : now();
        } catch (\Throwable $e) {
            $purchaseDate = now();
        }

        $customerUsername = Arr::get($subject, 'customer.username');
        $playerName = null;

        if (is_string($customerUsername) && $customerUsername !== '') {
            $playerName = $customerUsername;
        } elseif (is_array($customerUsername)) {
            $u = Arr::get($customerUsername, 'username');
            if (is_string($u) && $u !== '') {
                $playerName = $u;
            }
        }

        if ($playerName === null) {
            $playerName = (string) (Arr::get($subject, 'username') ?? Arr::get($subject, 'customer.email') ?? 'unknown');
        }

        $currency = Arr::get($subject, 'price_paid.currency')
            ?? Arr::get($subject, 'price.currency')
            ?? null;
        $currency = is_string($currency) && $currency !== '' ? $currency : null;

        $amount = Arr::get($subject, 'price_paid.amount')
            ?? Arr::get($subject, 'price.amount')
            ?? 0;
        $amountValue = is_numeric($amount) ? (float) $amount : 0.0;

        $products = Arr::get($subject, 'products');
        $firstProduct = is_array($products) && isset($products[0]) && is_array($products[0]) ? $products[0] : [];

        $packageName = Arr::get($firstProduct, 'name');
        $packageName = is_string($packageName) && $packageName !== '' ? $packageName : null;

        $packageId = Arr::get($firstProduct, 'id');
        $packageId = is_numeric($packageId) ? (int) $packageId : null;

        $attributes = [
            'player_uuid' => null,
            'player_name' => $playerName,
            'package_id' => $packageId,
            'package_name' => $packageName,
            'amount' => $amountValue,
            'currency' => $currency,
            'purchase_date' => $purchaseDate,
            'status' => $normalizedStatus,
            'raw_payload' => $payload,
        ];

        $existing = Transaction::query()->where('tebex_transaction_id', $transactionId)->first();

        if ($existing === null) {
            Transaction::create(array_merge($attributes, [
                'tebex_transaction_id' => $transactionId,
            ]));

            return ['created' => 1, 'updated' => 0, 'ignored' => 0, 'transaction_id' => $transactionId];
        }

        $existing->fill($attributes)->save();

        return ['created' => 0, 'updated' => 1, 'ignored' => 0, 'transaction_id' => $transactionId];
    }

    private function normalizeStatus(string $status): string {
        $s = strtolower(trim($status));

        return match (true) {
            str_contains($s, 'complete') => 'complete',
            str_contains($s, 'completed') => 'complete',
            str_contains($s, 'refund') => 'refund',
            str_contains($s, 'chargeback') => 'chargeback',
            str_contains($s, 'declin') => 'declined',
            str_contains($s, 'pending') => 'pending',
            default => 'complete',
        };
    }
}

