<?php

namespace Laracl;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laracl');

        // php artisan vendor:publish --tag=laracl-config
        $this->publishes([__DIR__.'/config/laracl.php' => config_path('laracl.php')], 'laracl-config');

        // php artisan vendor:publish --tag=laracl-buttons
        $this->publishes([__DIR__.'/resources/views/buttons' => resource_path('views/laracl/buttons')], 'laracl-buttons');

        // php artisan vendor:publish --tag=laracl-migrations
        $this->publishes([__DIR__.'/database/migrations' => database_path('migrations')], 'laracl-migrations');

        // php artisan vendor:publish --tag=laracl-cruds
        $this->publishes([__DIR__.'/resources/views/users' => resource_path('views/laracl/cruds/users')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/users-permissions' => resource_path('views/laracl/cruds/users-permissions')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/groups' => resource_path('views/laracl/cruds/groups')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/groups-permissions' => resource_path('views/laracl/cruds/groups-permissions')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/messages' => resource_path('views/laracl/cruds/messages')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/document.blade.php' => resource_path('views/laracl/cruds/document.blade.php')], 'laracl-cruds');

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
        $config_file = env('LARACL_CONFIG_FILE', __DIR__.'/config/laracl.php');
        $this->mergeConfigFrom($config_file, 'laracl');

        \Laracl\Core::normalizeConfig();
    }
}
