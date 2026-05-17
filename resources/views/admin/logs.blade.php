@extends('admin.layouts.admin')

@section('title', trans('tebexrewards::messages.admin.nav.logs'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">{{ trans('tebexrewards::messages.admin.logs.title') }}</h1>
            <p class="text-muted mb-0">{{ trans('tebexrewards::messages.admin.logs.intro') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('tebexrewards.admin.settings') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-gear"></i> {{ trans('tebexrewards::messages.admin.nav.settings') }}
            </a>
        </div>
    </div>

    @if(! $logsTableReady)
        <div class="alert alert-warning shadow-sm" role="alert">
            <h2 class="h6 alert-heading">{{ trans('tebexrewards::messages.admin.logs.migration_required_title') }}</h2>
            <p class="mb-2 small">{{ trans('tebexrewards::messages.admin.logs.migration_required_body') }}</p>
            <code class="d-block small p-2 bg-dark text-white rounded">php artisan migrate --path=plugins/tebexrewards/database/migrations/2026_05_16_000001_create_tebex_event_logs_table.php</code>
            <p class="mb-0 mt-2 small text-muted">{{ trans('tebexrewards::messages.admin.logs.migration_required_hint') }}</p>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ trans('tebexrewards::messages.admin.logs.stat_webhook_24h') }}</div>
                    <div class="fs-4 fw-semibold">{{ number_format($stats['webhook_24h']) }}</div>
                    <div class="text-muted small">{{ trans('tebexrewards::messages.admin.logs.stat_webhook_ok_24h', ['count' => $stats['webhook_ok_24h']]) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ trans('tebexrewards::messages.admin.logs.stat_transactions_24h') }}</div>
                    <div class="fs-4 fw-semibold">{{ number_format($stats['transactions_24h']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ trans('tebexrewards::messages.admin.logs.stat_headless_24h') }}</div>
                    <div class="fs-4 fw-semibold">{{ number_format($stats['headless_24h']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ trans('tebexrewards::messages.admin.logs.stat_last_webhook') }}</div>
                    <div class="fw-semibold">
                        @if($stats['last_webhook_at'])
                            {{ \Carbon\Carbon::parse($stats['last_webhook_at'])->diffForHumans() }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ trans('tebexrewards::messages.admin.logs.health_title') }}</h2>
            <ul class="list-unstyled mb-0 small">
                <li class="mb-2">
                    <i class="bi {{ $hasWebhookSecret ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }}"></i>
                    {{ trans('tebexrewards::messages.admin.logs.health_webhook_secret') }}
                </li>
                <li class="mb-2">
                    <i class="bi {{ $hasPublicToken ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }}"></i>
                    {{ trans('tebexrewards::messages.admin.logs.health_public_token') }}
                </li>
                <li class="mb-2">
                    <i class="bi {{ $hasPrivateKey ? 'bi-check-circle-fill text-success' : 'bi-exclamation-circle-fill text-warning' }}"></i>
                    {{ trans('tebexrewards::messages.admin.logs.health_private_key') }}
                </li>
                <li>
                    <span class="text-muted">{{ trans('tebexrewards::messages.admin.fields.webhook_endpoint_url') }}:</span>
                    <code class="user-select-all">{{ $webhookUrl }}</code>
                </li>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ trans('tebexrewards::messages.admin.quick_actions.title') }}</h2>
            <p class="text-muted small mb-3">{{ trans('tebexrewards::messages.admin.quick_actions.intro') }}</p>
            <div class="d-flex flex-wrap gap-2">
                <form action="{{ route('tebexrewards.admin.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-arrow-down"></i> {{ trans('tebexrewards::messages.admin.actions.force_sync') }}
                    </button>
                </form>
                <form action="{{ route('tebexrewards.admin.settings.clear_cache') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">{{ trans('tebexrewards::messages.admin.actions.clear_cache') }}</button>
                </form>
                @if($logsTableReady)
                    <form action="{{ route('tebexrewards.admin.logs.clear') }}" method="POST" onsubmit="return confirm(@json(trans('tebexrewards::messages.admin.logs.clear_confirm')));">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">{{ trans('tebexrewards::messages.admin.logs.clear_btn') }}</button>
                    </form>
                @endif
            </div>
            <small class="form-text text-muted d-block mt-3">{{ trans('tebexrewards::messages.admin.quick_actions.sync_help') }}</small>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ trans('tebexrewards::messages.admin.logs.table_title') }}</h2>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ trans('tebexrewards::messages.admin.logs.col_time') }}</th>
                            <th>{{ trans('tebexrewards::messages.admin.logs.col_channel') }}</th>
                            <th>{{ trans('tebexrewards::messages.admin.logs.col_level') }}</th>
                            <th>{{ trans('tebexrewards::messages.admin.logs.col_event') }}</th>
                            <th>{{ trans('tebexrewards::messages.admin.logs.col_message') }}</th>
                            <th>{{ trans('tebexrewards::messages.admin.logs.col_details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="text-nowrap small">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td><span class="badge bg-secondary">{{ $log->channel }}</span></td>
                                <td><span class="badge bg-{{ $log->levelBadgeClass() }}">{{ $log->level }}</span></td>
                                <td class="font-monospace small">{{ $log->event }}</td>
                                <td class="small">{{ $log->message }}</td>
                                <td class="small">
                                    @if(is_array($log->context) && $log->context !== [])
                                        <details>
                                            <summary class="text-primary" style="cursor:pointer">{{ trans('tebexrewards::messages.admin.logs.view_context') }}</summary>
                                            <pre class="mb-0 mt-1 p-2 bg-light border rounded small" style="max-height:120px;overflow:auto">{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </details>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ trans('tebexrewards::messages.admin.logs.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logsTableReady)
                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
