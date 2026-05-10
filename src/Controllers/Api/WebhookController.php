<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Tebexrewards\Services\WebhookIngestionService;
use Azuriom\Plugin\Tebexrewards\Services\WebhookSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller {

    public function handle(Request $request, WebhookIngestionService $ingestion) {

        $raw = (string) $request->getContent();
        $payload = json_decode($raw, true);

        if (! is_array($payload)) {
            return response()->json(['message' => trans('tebexrewards::messages.webhook.invalid_payload')], 422);
        }

        $secret = $this->getWebhookSecretOrNull();
        if ($secret === null) {
            return response()->json(['message' => trans('tebexrewards::messages.webhook.missing_secret')], 422);
        }

        $provided = (string) ($request->header('X-Signature') ?? $request->header('X-Tebex-Signature') ?? '');
        $provided = trim($provided);
        if (str_starts_with(strtolower($provided), 'sha256=')) {
            $provided = substr($provided, 7);
        }

        $expected = WebhookSignature::compute($raw, $secret);

        if (! WebhookSignature::equals($expected, $provided)) {
            Log::warning('TebexRewards: invalid webhook signature', [
                'provided' => $provided,
            ]);

            return response()->json(['message' => trans('tebexrewards::messages.webhook.invalid_signature')], 401);
        }

        if (($payload['type'] ?? null) === 'validation.webhook') {
            $id = (string) ($payload['id'] ?? '');

            return response()->json(['id' => $id], 200);
        }

        $result = $ingestion->ingest($payload);

        return response()->json([
            'message' => trans('tebexrewards::messages.webhook.ok'),
            'result' => $result,
        ]);
    }

    private function getWebhookSecretOrNull(): ?string {
        $encrypted = (string) setting('tebexrewards.webhook_secret', '');

        if ($encrypted === '') {
            return null;
        }

        try {
            $secret = Crypt::decryptString($encrypted);
        } catch (\Throwable $e) {
            return null;
        }

        if ($secret === '') {
            return null;
        }

        return $secret;
    }
}

