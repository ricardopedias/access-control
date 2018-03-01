<?php 

namespace Laracl;

use Gate;

/**
 * ...
 */
class Accessor
{
    /**
     * Dados das permissões atualmente invocadas
     * 
     * @var array
     */
    protected $current_ability = null;

    /**
     * Origem das permissões atualmente invocadas
     */
    protected $current_ability_origin = null;

    /**
     * Carrega e inclui os helpers do pacote
     * 
     * @return void
     */
    public function loadHelpers()
    {
        include('helpers.php');
    }

    /**
     * Carrega e registra as diretivas para o blade
     * 
     * @return void
     */
    public function loadBladeDirectives()
    {
        include('directives.php');
    }

    /**
     * Regitra os verificadores de acesso com base na configuração
     * 
     * @return void
     */
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

                Gate::define("{$role}.{$permission}", function ($user, $callback = null) 
                                                           use ($role, $permission) 
                {
                    // Usuário permamentemente liberado
                    $root_user = config('laracl.root_user');
                    if ( $user->id == $root_user) {
                        $this->setCurrentAbilityOrigin('config');
                        $this->setCurrentAbility($role, $permission, true);
                        return true;
                    }

                    // Passou na verificação adicional?
                    if ($callback != null && is_callable($callback) && $callback() !== true) {
                        $this->setCurrentAbilityOrigin('callback');
                        $this->setCurrentAbility($role, $permission, false);
                        return false;
                    }

                    // Existem permissões setadas?
                    $user_abilities = $this->getUserPermissions($user->id, $role);
                    if ($user_abilities === null) {
                        $this->setCurrentAbility($role, $permission, false);
                        return false;
                    }

                    // create,edit,show ou delete == yes?
                    $result = (isset($user_abilities['permissions']) && $user_abilities['permissions'][$permission] == 'yes');
                    $this->setCurrentAbility($role, $permission, $result);
                    return $result;
                });
            }
        }
    }

    /**
     * Salva os dados da última verificação por privilégios 
     * O formato procede assim: users.edit = {$role}.{$permission}
     * 
     * @return boolean
     */
    public function setCurrentAbility($role, $permission, $granted)
    {
        $this->current_ability = [
            'role'       => $role,
            'permission' => $permission,
            'granted'    => $granted,
            ];

        return $granted;
    }

    /**
     * Devolve os dados da última verificação por privilégios 
     * 
     * @return array
     */
    public function getCurrentAbility()
    {
        return $this->current_ability;
    }

    /**
     * Salva a origem da última verificação por privilégios 
     * 
     * @param string $origin Possibilidades: config, callback, user, group
     * @return boolean
     */
    public function setCurrentAbilityOrigin($origin)
    {
        $this->current_ability_origin = $origin;
    }

    /**
     * Devolve a origem da última verificação por privilégios 
     * As possibilidades são: config, callback, user, group
     * 
     * @return string 
     */
    public function getCurrentAbilityOrigin()
    {
        return $this->current_ability_origin;
    }

    /**
     * Devolve as permissões para o usuário n função especificada
     * O formato procede assim: users.edit = {$role_slug}.edit
     * 
     * @return booelan
     */
    public function getUserPermissions($user_id, $role_slug)
    {
        if (session('user.abilities') == null) {

            // Gera um cache de permissões
            // para evitar consultas ao banco de dados
            $roles = \Laracl\Models\AclRole::all();

            $cache_all   = [];
            $cache_slugs = [];

            foreach($roles as $item) {
                $cache_all[$item->slug] = $item->toArray();
                $cache_slugs[$item->id] = $item->slug;
            }

            // As permissões setadas para o usuário tem precedencia
            $user_permissions = \Laracl\Models\AclUserPermission::collectByUser($user_id);
            if ($user_permissions->count() > 0) {
                foreach($user_permissions as $item) {
                    if (isset($cache_slugs[$item->role_id])) {
                        $slug = $cache_slugs[$item->role_id];
                        $cache_all[$slug]['permissions'] = $item->toArray();
                    }
                }

                $this->setCurrentAbilityOrigin('user');
            }
            // Quando não existem permissões setadas para o usuário, 
            // as permissões do grupo são usadas no lugar
            else {

                $group_permissions = \Laracl\Models\AclGroupPermission::collectByUser($user_id);
                foreach($group_permissions as $item) {
                    if (isset($cache_slugs[$item->role_id])) {
                        $slug = $cache_slugs[$item->role_id];
                        $cache_all[$slug]['permissions'] = $item->toArray();
                    }
                }

                $this->setCurrentAbilityOrigin('group');
            }

            session([ 'user.abilities' => collect($cache_all) ]);
        }

        $user_abilities = session('user.abilities');

        return $user_abilities[$role_slug] ?? null;
    }
}
