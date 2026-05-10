<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Azuriom\Models\User;
use Azuriom\Plugin\Tebexrewards\Models\Transaction;
use Azuriom\Plugin\Tebexrewards\Support\TebexRewardsCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;

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

        $playerName = $this->resolvePlayerName($subject);
        $playerUuid = $this->resolvePlayerUuid($subject);

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
            'player_uuid' => $playerUuid,
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
            TebexRewardsCache::invalidateAll();

            return ['created' => 1, 'updated' => 0, 'ignored' => 0, 'transaction_id' => $transactionId];
        }

        $existing->fill($attributes)->save();
        TebexRewardsCache::invalidateAll();

        return ['created' => 0, 'updated' => 1, 'ignored' => 0, 'transaction_id' => $transactionId];
    }

    /**
     * @param  array<string, mixed>  $subject
     */
    private function resolvePlayerName(array $subject): string {
        $customerUsername = Arr::get($subject, 'customer.username');
        if (is_string($customerUsername) && $customerUsername !== '' && ! $this->looksLikeEmail($customerUsername)) {
            return $customerUsername;
        }
        if (is_array($customerUsername)) {
            $u = Arr::get($customerUsername, 'username');
            if (is_string($u) && $u !== '') {
                return $u;
            }
        }

        $top = Arr::get($subject, 'username');
        if (is_string($top) && $top !== '' && ! $this->looksLikeEmail($top)) {
            return $top;
        }

        $products = Arr::get($subject, 'products');
        if (is_array($products)) {
            foreach ($products as $product) {
                if (! is_array($product)) {
                    continue;
                }
                $pu = Arr::get($product, 'username');
                if (is_string($pu) && $pu !== '' && ! $this->looksLikeEmail($pu)) {
                    return $pu;
                }
                if (is_array($pu)) {
                    $u = Arr::get($pu, 'username');
                    if (is_string($u) && $u !== '') {
                        return $u;
                    }
                }
            }
        }

        $email = Arr::get($subject, 'customer.email');
        if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fromSiteUser = User::query()->where('email', $email)->value('name');
            if (is_string($fromSiteUser) && $fromSiteUser !== '') {
                return $fromSiteUser;
            }
        }

        return 'unknown';
    }

    /**
     * @param  array<string, mixed>  $subject
     */
    private function resolvePlayerUuid(array $subject): ?string {
        $candidates = [
            Arr::get($subject, 'customer.username.id'),
        ];

        $products = Arr::get($subject, 'products');
        if (is_array($products)) {
            foreach ($products as $product) {
                if (is_array($product)) {
                    $candidates[] = Arr::get($product, 'username.id');
                }
            }
        }

        foreach ($candidates as $id) {
            if (is_string($id) && trim($id) !== '') {
                return trim($id);
            }
        }

        return null;
    }

    private function looksLikeEmail(string $value): bool {
        return str_contains($value, '@') && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
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

