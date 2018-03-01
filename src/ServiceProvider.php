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
        
        // php artisan vendor:publish --tag=laracl-config
        $this->publishes([__DIR__.'/config/laracl.php' => config_path('laracl.php')], 'laracl-config');

        // php artisan vendor:publish --tag=laracl-buttons
        $this->publishes([__DIR__.'/resources/views/buttons' => resource_path('views/laracl/buttons')], 'laracl-buttons');

        // php artisan vendor:publish --tag=laracl-cruds
        $this->publishes([__DIR__.'/resources/views/users' => resource_path('views/laracl/cruds/users')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/users-permissions' => resource_path('views/laracl/cruds/users-permissions')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/groups' => resource_path('views/laracl/cruds/groups')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/groups-permissions' => resource_path('views/laracl/cruds/groups-permissions')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/messages' => resource_path('views/laracl/cruds/messages')], 'laracl-cruds');
        $this->publishes([__DIR__.'/resources/views/document.blade.php' => resource_path('views/laracl/cruds/document.blade.php')], 'laracl-cruds');

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
    }

    /**
     * Gera a estrutura para nomeamento de rotas para os CRUDs, 
     * com base nas urls especificadas na configuração.
     * 
     * Por exemplo, no item ['users' => 'painel/usuarios'], 
     * serão extraidos os indices e os nomes para as rotas dos CRUDs, 
     * ficando assim:
     * [
     *     laracl.routes.users.base  =>  users
     *     laracl.routes.users.index  => usuarios.index
     *     laracl.routes.users.create => usuarios.create
     *     laracl.routes.users.store  => usuarios.store
     *     laracl.routes.users.edit   => usuarios.edit
     *     laracl.routes.users.update => usuarios.update
     *     laracl.routes.users.delete => usuarios.delete
     * ]
     */
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
