<?php 

namespace Laracl;

use Gate;

/**
 * ...
 */
class Accessor
{
    protected $log = [];

    public function registerPolicies()
    {
        $roles_list = config('laracl.roles');

        if ($roles_list === null) {
            throw new \Exception("You need to add the 'roles' in the Laracl configuration", 1);
        }

        foreach ($roles_list as $role => $info) {

            $label = $info['label'];
            $allowed_permissions = explode(',', trim($info['permissions'], ',') );

            foreach ($allowed_permissions as $permission) {

                Gate::define("{$role}.{$permission}", function ($user, $callback = null) use ($role, $permission) {
                    
                    // Em modo de desenvolvimento o usuário 1 é liberado
                    if ( (env('APP_DEBUG') || env('APP_ENV') === 'local') && $user->id == 1) {
                        return true;
                    }

                    // Passou na verificação adicional?
                    if ($callback != null && is_callable($callback) && $callback() !== true) {
                        self::setCurrentPermissions($role, $permission, false);
                        return false;
                    }

                    $user_permissions = \Laracl\Models\AclUserPermission::collectByUserRole($user->id, $role);

                    // Existem permissões setadas?
                    if ($user_permissions->count() == 0) {
                        self::setCurrentPermissions($role, $permission, false);
                        return false;
                    }

                    // create,edit,show ou delete == yes?
                    $result = ($user_permissions->where($permission, 'yes')->count() > 0);
                    self::setCurrentPermissions($role, $permission, $result);
                    return $result;
                });    
            }
        }
    }

    public function loadHelpers()
    {
        include('helpers.php');
    }

    public function loadBladeDirectives()
    {
        include('directives.php');
    }


    public function setCurrentPermissions($role, $permission, $granted)
    {
        $this->log[] = [
            'role'       => $role,
            'permission' => $permission,
            'granted'    => $granted,
            ];
    }

    public function getCurrentPermissions()
    {
        return $this->log;
    }
}
