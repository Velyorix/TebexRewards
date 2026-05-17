<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Tebexrewards\Models\EventLog;
use Azuriom\Plugin\Tebexrewards\Services\EventLogService;
use Azuriom\Plugin\Tebexrewards\Services\WebhookIngestionService;
use Azuriom\Plugin\Tebexrewards\Services\WebhookSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(
        Request $request,
        WebhookIngestionService $ingestion,
        EventLogService $eventLogs,
    ) {
        $raw = (string) $request->getContent();
        $payload = json_decode($raw, true);

        if (! is_array($payload)) {
            $eventLogs->log(
                EventLog::CHANNEL_WEBHOOK,
                'payload.invalid',
                'Webhook rejected: invalid JSON payload',
                EventLog::LEVEL_WARNING,
                null,
                $request
            );

            return response()->json(['message' => trans('tebexrewards::messages.webhook.invalid_payload')], 422);
        }

        $summary = EventLogService::webhookPayloadSummary($payload);

        $secret = $this->getWebhookSecretOrNull();
        if ($secret === null) {
            $eventLogs->log(
                EventLog::CHANNEL_WEBHOOK,
                'secret.missing',
                'Webhook rejected: webhook secret not configured in admin',
                EventLog::LEVEL_ERROR,
                ['type' => $summary['type'] ?? null],
                $request
            );

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
                'type' => $summary['type'] ?? null,
            ]);

            $eventLogs->log(
                EventLog::CHANNEL_WEBHOOK,
                'signature.invalid',
                'Webhook rejected: HMAC signature mismatch',
                EventLog::LEVEL_ERROR,
                $summary,
                $request
            );

            return response()->json(['message' => trans('tebexrewards::messages.webhook.invalid_signature')], 401);
        }

        if (($payload['type'] ?? null) === 'validation.webhook') {
            $id = (string) ($payload['id'] ?? '');

            $eventLogs->log(
                EventLog::CHANNEL_WEBHOOK,
                'validation.ok',
                'Tebex endpoint validation succeeded',
                EventLog::LEVEL_INFO,
                ['webhook_id' => $id],
                $request
            );

            return response()->json(['id' => $id], 200);
        }

        $result = $ingestion->ingest($payload);

        $event = match (true) {
            ($result['created'] ?? 0) > 0 => 'payment.created',
            ($result['updated'] ?? 0) > 0 => 'payment.updated',
            default => 'payment.ignored',
        };

        $level = ($result['ignored'] ?? 0) > 0 ? EventLog::LEVEL_WARNING : EventLog::LEVEL_INFO;

        $message = match ($event) {
            'payment.created' => 'Payment stored from webhook',
            'payment.updated' => 'Payment updated from webhook',
            default => 'Webhook accepted but no transaction was stored',
        };

        $eventLogs->log(
            EventLog::CHANNEL_WEBHOOK,
            $event,
            $message,
            $level,
            array_merge($summary, $result),
            $request
        );

        return response()->json([
            'message' => trans('tebexrewards::messages.webhook.ok'),
            'result' => $result,
        ]);
    }

    private function getWebhookSecretOrNull(): ?string
    {
        $secret = \Azuriom\Plugin\Tebexrewards\Support\TebexCredentials::decrypt(
            \Azuriom\Plugin\Tebexrewards\Support\TebexCredentials::SETTING_WEBHOOK_SECRET
        );

        return $secret !== '' ? $secret : null;
    }
}
