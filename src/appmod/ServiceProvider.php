<?php
namespace Acl;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private function modPath($path)
    {
        $ds = DIRECTORY_SEPARATOR;
        return dirname(__DIR__) . $ds . str_replace('\\', $ds, $path);
    }
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom($this->modPath('routes.php'));
        $this->loadMigrationsFrom($this->modPath('database/migrations'));
        $this->loadViewsFrom($this->modPath('resources/views'), 'acl');

        // php artisan vendor:publish --tag=acl-config
        $this->publishes([$this->modPath('/config/acl.php') => config_path('acl.php')], 'acl-config');

        // php artisan vendor:publish --tag=acl-buttons
        $this->publishes([$this->modPath('resources/views/buttons') => resource_path('views/acl/buttons')], 'acl-buttons');

        // php artisan vendor:publish --tag=acl-migrations
        $this->publishes([$this->modPath('database/publish') => database_path('migrations')], 'acl-migrations');

        // php artisan vendor:publish --tag=acl-cruds
        $this->publishes([$this->modPath('resources/views/users') => resource_path('views/acl/cruds/users')], 'acl-cruds');
        $this->publishes([$this->modPath('resources/views/users-permissions') => resource_path('views/acl/cruds/users-permissions')], 'acl-cruds');
        $this->publishes([$this->modPath('resources/views/groups') => resource_path('views/acl/cruds/groups')], 'acl-cruds');
        $this->publishes([$this->modPath('resources/views/groups-permissions') => resource_path('views/acl/cruds/groups-permissions')], 'acl-cruds');
        $this->publishes([$this->modPath('resources/views/messages') => resource_path('views/acl/cruds/messages')], 'acl-cruds');
        $this->publishes([$this->modPath('resources/views/document.blade.php') => resource_path('views/acl/cruds/document.blade.php')], 'acl-cruds');

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
        // Adiciona as configurações padrões no namespace acl
        $config_file = env('LARACL_CONFIG_FILE', $this->modPath('config/acl.php'));
        $this->mergeConfigFrom($config_file, 'acl');

        \Acl\Core::normalizeConfig();
    }
}
