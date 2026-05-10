<?php

namespace Azuriom\Plugin\Tebexrewards\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;

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
        // $this->registerMiddleware();

        //
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

        $this->registerRouteDescriptions();

        $this->registerAdminNavigation();

        $this->registerUserNavigation();

        //
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array<string, string>
     */
    protected function routeDescriptions(): array
    {
        return [
            'tebexrewards.index' => trans('tebexrewards::messages.nav.leaderboard'),
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
                    'tebexrewards.admin.settings' => trans('tebexrewards::messages.admin.nav.settings'),
                    'tebexrewards.admin.leaderboard' => trans('tebexrewards::messages.admin.nav.leaderboard'),
                    'tebexrewards.admin.progress' => trans('tebexrewards::messages.admin.nav.progress'),
                    'tebexrewards.admin.ranks' => trans('tebexrewards::messages.admin.nav.ranks'),
                ],
            ],
        ];
    }

    /**
     * Return the user navigations routes to register in the user menu.
     *
     * @return array<string, array<string, string>>
     */
    protected function userNavigation(): array
    {
        return [
            'tebexrewards' => [
                'route' => 'tebexrewards.index',
                'name' => trans('tebexrewards::messages.nav.leaderboard'),
                'icon' => 'bi bi-trophy',
            ],
        ];
    }
}
