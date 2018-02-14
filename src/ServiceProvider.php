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
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laracl');
        
        // Config
        // php artisan vendor:publish
        $this->publishes([__DIR__.'/config/laracl.php' => config_path('laracl.php')]);
        
        // Views
        // php artisan vendor:publish
        //$this->publishes([__DIR__.'/resources/views' => resource_path('views/plexi/foundation')]);
        
        // Assets
        //$this->publishes([__DIR__.'/public' => public_path('plexi/foundation')]);
        
        // Alternativa agrupada em 'public' (pode ser qualquer palavra)
        // php artisan vendor:publish --tag=public --force
        //$this->publishes([__DIR__.'/public' => public_path('plexi/foundation')], 'public');

        //$roles_list = \App::runningInConsole() == true ? [] : config('laracl.roles');

        $roles_list = config('laracl.roles');

        if ($roles_list === null) {
            throw new \Exception("You need to add the 'roles' in the Laracl configuration", 1);
        }

        foreach ($roles_list as $role => $info) {

            $label = $info['label'];
            $allowed_permissions = explode(',', trim($info['permissions'], ',') );

            foreach ($allowed_permissions as $permission) {

                Gate::define("{$role}.{$permission}", function ($user, $callback = null) use ($role, $permission) {

                    // Passou na verificação adicional?
                    if ($callback != null && is_callable($callback) && $callback() !== true) {
                        \Laracl::setCurrentPermissions($role, $permission, false);
                        return false;
                    }

                    $user_permissions = \Laracl\Models\AclPermission::collectByUserRole($user->id, $role);

                    // Existem permissões setadas?
                    if ($user_permissions->count() == 0) {
                        \Laracl::setCurrentPermissions($role, $permission, false);
                        return false;
                    }

                    // create,edit,show ou delete == yes?
                    $result = ($user_permissions->where($permission, 'yes')->count() > 0);
                    \Laracl::setCurrentPermissions($role, $permission, $result);
                    return $result;
                });    
            }
        }
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
