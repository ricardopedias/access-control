<?php

namespace Laracl;

use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
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

        // php artisan vendor:publish --tag=laracl-migrations
        $this->publishes([__DIR__.'/database/migrations' => database_path('migrations')], 'laracl-migrations');

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
        $config_file = env('LARACL_CONFIG_FILE', __DIR__.'/config/laracl.php');
        $this->mergeConfigFrom($config_file, 'laracl');

        $this->normalizeConfig();
    }

    /**
     * Gera a estrutura para nomeamento de rotas para os CRUDs, 
     * com base nas urls especificadas na configuração.
     * 
     * Por exemplo:
     * 
     * 'routes'     => [
     *      'users'              => 'painel/users',
     *      'users-permissions'  => 'painel/users-permissions',
     *      'groups'             => 'painel/groups',
     *      'groups-permissions' => 'painel/groups-permissions',
     * ]
     * 
     * No item ['users' => 'painel/users'], serão extraidos 
     * os indices e os nomes para as rotas dos CRUDs, ficando assim:
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

        // A configuração só pode ser normalizada uma vez
        // se a primeira rota já for um array, encerra a operação
        $first_route = current($config['routes']);
        if (is_array($first_route)) {
            return false;
        }

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

        return true;
    }
}
