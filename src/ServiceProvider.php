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
        $this->publishes([__DIR__.'/config/laracl.php' => config_path('laracl.php')], 'config-laracl');
        
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
        
        $this->normalizeConfig();

        //$this->app->make('Plexi\Foundation\Http\Controllers\ExampleController');
    }

    protected function normalizeConfig()
    {
        $config = config('laracl');

        foreach ($config['routes'] as $slug => $nulled) {

            // admin/users -> 'users'
            $route_base = preg_replace('#.*/#', '', $config['routes'][$slug]);

            $route_params = [
                "laracl.routes.{$slug}.base"   => $config['routes'][$slug],
                "laracl.routes.{$slug}.index"  => $route_base . ".index",
                "laracl.routes.{$slug}.create" => $route_base . ".create",
                "laracl.routes.{$slug}.store"  => $route_base . ".store",
                "laracl.routes.{$slug}.edit"   => $route_base . ".edit",
                "laracl.routes.{$slug}.update" => $route_base . ".update",
                "laracl.routes.{$slug}.delete" => $route_base . ".delete",
            ];
            config($route_params);
        }
    }
}
