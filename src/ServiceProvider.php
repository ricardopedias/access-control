<?php

namespace Laracl;

use Illuminate\Support\Facades\Gate;
use DB;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        \Laracl::loadHelpers();
        
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laracl');
        
        // Config
        // php artisan vendor:publish
        $this->publishes([__DIR__.'/config/laracl.php' => config_path('laracl.php')], 'laracl');
        
        // Views
        // php artisan vendor:publish --provider="Laracl\ServiceProvider"
        //$this->publishes([__DIR__.'/resources/views' => resource_path('views/plexi/foundation')]);
        
        // Assets
        //$this->publishes([__DIR__.'/public' => public_path('plexi/foundation')]);
        
        // Alternativa agrupada em 'public' (pode ser qualquer palavra)
        // php artisan vendor:publish --tag=public --force
        //$this->publishes([__DIR__.'/public' => public_path('plexi/foundation')], 'public');

        //$roles_list = \App::runningInConsole() == true ? [] : config('laracl.roles');

        \Laracl::registerPolicies();

        \Laracl::loadBladeDirectives();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Adiciona as configurações padrões no namespace laracl
        $this->mergeConfigFrom(__DIR__.'/config/laracl.php', 'laracl');
        
        //$this->app->make('Plexi\Foundation\Http\Controllers\ExampleController');
        
    }
}
