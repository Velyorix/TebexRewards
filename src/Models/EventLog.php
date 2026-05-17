<?php

namespace Azuriom\Plugin\Tebexrewards\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $channel
 * @property string $level
 * @property string $event
 * @property string $message
 * @property array|null $context
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon $created_at
 */
class EventLog extends Model
{
    public const CHANNEL_WEBHOOK = 'webhook';

    public const CHANNEL_HEADLESS = 'headless';

    public const LEVEL_INFO = 'info';

    public const LEVEL_WARNING = 'warning';

    public const LEVEL_ERROR = 'error';

    public const UPDATED_AT = null;

    protected $table = 'tebex_event_logs';

    protected $fillable = [
        'channel',
        'level',
        'event',
        'message',
        'context',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    public function scopeWebhook(Builder $query): Builder
    {
        return $query->where('channel', self::CHANNEL_WEBHOOK);
    }

    public function scopeHeadless(Builder $query): Builder
    {
        return $query->where('channel', self::CHANNEL_HEADLESS);
    }

    public function levelBadgeClass(): string
    {
        return match ($this->level) {
            self::LEVEL_ERROR => 'danger',
            self::LEVEL_WARNING => 'warning',
            default => 'success',
        };
    }
}
