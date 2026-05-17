<?php

namespace Azuriom\Plugin\Tebexrewards\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Plugin\Tebexrewards\Console\SyncTebexCommand;
use Azuriom\Plugin\Tebexrewards\Http\View\Composers\TebexrewardsProfileComposer;
use Azuriom\Plugin\Tebexrewards\Services\DonorRankService;
use Azuriom\Plugin\Tebexrewards\Services\EventLogService;
use Azuriom\Plugin\Tebexrewards\Services\GoalProgressService;
use Azuriom\Plugin\Tebexrewards\Services\PaymentSyncService;
use Azuriom\Plugin\Tebexrewards\Services\SyncService;
use Azuriom\Plugin\Tebexrewards\Services\TebexApiService;
use Azuriom\Plugin\Tebexrewards\Services\TebexPluginApiService;
use Azuriom\Plugin\Tebexrewards\Services\WebhookIngestionService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class TebexrewardsServiceProvider extends BasePluginServiceProvider
{
    /**
     * The plugin's global HTTP middleware stack.
     */
    protected array $middleware = [
        // \Azuriom\Plugin\Tebexrewards\Middleware\ExampleMiddleware::class,
    ];

    /**
     * The plugin's route middleware groups.
     */
    protected array $middlewareGroups = [];

    /**
     * The plugin's route middleware.
     */
    protected array $routeMiddleware = [
        // 'example' => \Azuriom\Plugin\Tebexrewards\Middleware\ExampleRouteMiddleware::class,
    ];

    /**
     * The policy mappings for this plugin.
     *
     * @var array<string, string>
     */
    protected array $policies = [
        // User::class => UserPolicy::class,
    ];

    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        require_once dirname(__DIR__).'/helpers.php';

        // $this->registerMiddleware();
        $this->app->singleton(TebexApiService::class, fn () => new TebexApiService());
        $this->app->singleton(TebexPluginApiService::class, fn () => new TebexPluginApiService());
        $this->app->singleton(EventLogService::class, fn () => new EventLogService());
        $this->app->singleton(PaymentSyncService::class, fn ($app) => new PaymentSyncService(
            $app->make(TebexPluginApiService::class),
            $app->make(WebhookIngestionService::class),
        ));
        $this->app->singleton(SyncService::class, fn ($app) => new SyncService(
            $app->make(TebexApiService::class),
            $app->make(PaymentSyncService::class),
        ));
        $this->app->singleton(WebhookIngestionService::class, fn () => new WebhookIngestionService());
        $this->app->singleton(GoalProgressService::class, fn () => new GoalProgressService());
        $this->app->singleton(DonorRankService::class, fn () => new DonorRankService());
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        $this->loadViews();

        $this->loadTranslations();

        $this->loadMigrations();

        $this->ensureEventLogsTable();

        $this->registerRouteDescriptions();

        $this->registerAdminNavigation();

        $this->registerUserNavigation();

        $this->commands([
            SyncTebexCommand::class,
        ]);

        $this->registerSchedule();

        View::composer('*', function (\Illuminate\View\View $view) {
            $name = $view->name();
            if (str_starts_with($name, 'admin.')) {
                return;
            }
            if ($name !== 'profile.index' && ! str_ends_with($name, '.profile.index')) {
                return;
            }

            app(TebexrewardsProfileComposer::class)->compose($view);
        });
    }

    protected function ensureEventLogsTable(): void
    {
        if (! is_installed() || Schema::hasTable('tebex_event_logs')) {
            return;
        }

        $migration = $this->pluginPath('database/migrations/2026_05_16_000001_create_tebex_event_logs_table.php');
        if (! is_file($migration)) {
            return;
        }

        try {
            $this->app->make('migrator')->run([$migration]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function schedule(Schedule $schedule) {
        $minutes = (int) setting('tebexrewards.sync_interval', 10);

        $event = $schedule->command('tebexrewards:sync');

        match ($minutes) {
            5 => $event->everyFiveMinutes(),
            10 => $event->everyTenMinutes(),
            30 => $event->everyThirtyMinutes(),
            60 => $event->hourly(),
            default => $event->everyTenMinutes(),
        };

        $event->withoutOverlapping(10);
    }

    /**
     * @return array<string, string>
     */
    protected function routeDescriptions(): array {
        return [
            'tebexrewards.index' => trans('tebexrewards::messages.route_descriptions.hub'),
            'tebexrewards.leaderboard' => trans('tebexrewards::messages.route_descriptions.leaderboard_only'),
        ];
    }

    /**
     * Return the admin navigations routes to register in the dashboard.
     *
     * @return array<string, array<string, string>>
     */
    protected function adminNavigation(): array
    {
        return [
            'tebexrewards' => [
                'name' => 'Tebex Rewards',
                'type' => 'dropdown',
                'icon' => 'bi bi-trophy-fill',
                'route' => 'tebexrewards.admin.*',
                'items' => [
                    'tebexrewards.admin.logs' => trans('tebexrewards::messages.admin.nav.logs'),
                    'tebexrewards.admin.settings' => trans('tebexrewards::messages.admin.nav.settings'),
                    'tebexrewards.admin.leaderboard' => trans('tebexrewards::messages.admin.nav.leaderboard'),
                    'tebexrewards.admin.progress' => trans('tebexrewards::messages.admin.nav.progress'),
                    'tebexrewards.admin.ranks' => trans('tebexrewards::messages.admin.nav.ranks'),
                ],
            ],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    protected function userNavigation(): array
    {
        $items = [];

        if ((bool) setting('tebexrewards.nav.user_hub', true)) {
            $items['tebexrewards_hub'] = [
                'route' => 'tebexrewards.index',
                'name' => trans('tebexrewards::messages.nav.hub'),
                'icon' => 'bi bi-grid-3x3-gap',
            ];
        }

        if ((bool) setting('tebexrewards.nav.user_leaderboard', true)) {
            $items['tebexrewards_leaderboard'] = [
                'route' => 'tebexrewards.leaderboard',
                'name' => trans('tebexrewards::messages.nav.leaderboard_only'),
                'icon' => 'bi bi-trophy',
            ];
        }

        return $items;
    }
}
