<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

class WebhookSignature
{
    public static function compute(string $rawBody, string $secret): string {

        $bodyHash = hash('sha256', $rawBody);

        return hash_hmac('sha256', $bodyHash, $secret);
    }

    public static function equals(string $expected, string $provided): bool {

        if ($provided === '' || $expected === '') {
            return false;
        }

        return hash_equals(strtolower($expected), strtolower($provided));
    }
}

