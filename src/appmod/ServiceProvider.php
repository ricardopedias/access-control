<?php
namespace Laracl;

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
        $this->loadViewsFrom($this->modPath('resources/views'), 'laracl');

        // php artisan vendor:publish --tag=laracl-config
        $this->publishes([$this->modPath('/config/laracl.php') => config_path('laracl.php')], 'laracl-config');

        // php artisan vendor:publish --tag=laracl-buttons
        $this->publishes([$this->modPath('resources/views/buttons') => resource_path('views/laracl/buttons')], 'laracl-buttons');

        // php artisan vendor:publish --tag=laracl-migrations
        $this->publishes([$this->modPath('database/migrations') => database_path('migrations')], 'laracl-migrations');

        // php artisan vendor:publish --tag=laracl-cruds
        $this->publishes([$this->modPath('resources/views/users') => resource_path('views/laracl/cruds/users')], 'laracl-cruds');
        $this->publishes([$this->modPath('resources/views/users-permissions') => resource_path('views/laracl/cruds/users-permissions')], 'laracl-cruds');
        $this->publishes([$this->modPath('resources/views/groups') => resource_path('views/laracl/cruds/groups')], 'laracl-cruds');
        $this->publishes([$this->modPath('resources/views/groups-permissions') => resource_path('views/laracl/cruds/groups-permissions')], 'laracl-cruds');
        $this->publishes([$this->modPath('resources/views/messages') => resource_path('views/laracl/cruds/messages')], 'laracl-cruds');
        $this->publishes([$this->modPath('resources/views/document.blade.php') => resource_path('views/laracl/cruds/document.blade.php')], 'laracl-cruds');

        \Laracl\Core::registerPolicies();

        \Laracl\Core::loadBladeDirectives();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Adiciona as configurações padrões no namespace laracl
        $config_file = env('LARACL_CONFIG_FILE', $this->modPath('config/laracl.php'));
        $this->mergeConfigFrom($config_file, 'laracl');

        \Laracl\Core::normalizeConfig();
    }
}
