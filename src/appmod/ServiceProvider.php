<?php
namespace Acl;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(Core::modPath('routes.php'));
        $this->loadMigrationsFrom(Core::modPath('database/migrations'));
        $this->loadViewsFrom(Core::modPath('resources/views'), 'acl');

        // php artisan vendor:publish --tag=acl-config
        $this->publishes([Core::modPath('/config/acl.php') => config_path('acl.php')], 'acl-config');

        // php artisan vendor:publish --tag=acl-buttons
        $this->publishes([Core::modPath('resources/views/buttons') => resource_path('views/acl/buttons')], 'acl-buttons');

        // php artisan vendor:publish --tag=acl-migrations
        $this->publishes([Core::modPath('database/publish') => database_path('migrations')], 'acl-migrations');

        // php artisan vendor:publish --tag=acl-cruds
        $this->publishes([Core::modPath('resources/views/users') => resource_path('views/acl/cruds/users')], 'acl-cruds');
        $this->publishes([Core::modPath('resources/views/users-permissions') => resource_path('views/acl/cruds/users-permissions')], 'acl-cruds');
        $this->publishes([Core::modPath('resources/views/groups') => resource_path('views/acl/cruds/groups')], 'acl-cruds');
        $this->publishes([Core::modPath('resources/views/groups-permissions') => resource_path('views/acl/cruds/groups-permissions')], 'acl-cruds');
        $this->publishes([Core::modPath('resources/views/messages') => resource_path('views/acl/cruds/messages')], 'acl-cruds');
        $this->publishes([Core::modPath('resources/views/document.blade.php') => resource_path('views/acl/cruds/document.blade.php')], 'acl-cruds');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Acl\Console\Commands\UserCreate::class,
                \Acl\Console\Commands\UserOn::class,
                \Acl\Console\Commands\UserOff::class,
                \Acl\Console\Commands\UserPassword::class,
                \Acl\Console\Commands\UserPanelOn::class,
                \Acl\Console\Commands\UserPanelOff::class,
            ]);
        }

        \Acl\Core::registerPolicies();

        \Acl\Core::loadBladeDirectives();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (Core::hasAdminPanel() === true && env('APP_ENV') !== 'testing') {
            // Se o modulo admin-panel estiver em uso,
            // as configurações são gerenciadas por ele
            $config_file = \Admin\Core::modPath('config/acl.php');
            $this->mergeConfigFrom($config_file, 'acl');

        } else {
            // Adiciona as configurações padrões no namespace acl
            $config_file = env('ACL_CONFIG_FILE', Core::modPath('config/acl.php'));
            $this->mergeConfigFrom($config_file, 'acl');
        }

        \Acl\Core::normalizeConfig();
    }
}
