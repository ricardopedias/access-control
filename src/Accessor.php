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
    protected $current_ability_origin = 'none';

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
     * Salva os dados da última verificação por privilégios
     * Na verificação: users.edit = {$role}.{$permission}
     *
     * @param string $role
     * @param string $permission
     * @param boolean $granted
     */
    public function setCurrentAbility(string $role, string $permission, bool $granted)
    {
        $this->current_ability = [
            'role'       => $role,
            'permission' => $permission,
            'granted'    => $granted,
            ];
    }

    /**
     * Devolve os dados da última verificação por privilégios
     *
     * @return array ou null
     */
    public function getCurrentAbility()
    {
        return $this->current_ability;
    }

    /**
     * Salva a origem da última verificação por privilégios.
     * Útil para verificações de debug e testes de unidade.
     *
     * @param string $origin Possibilidades: config, callback, user, group
     */
    public function setCurrentAbilityOrigin(string $origin)
    {
        $this->current_ability_origin = $origin;
    }

    /**
     * Devolve a origem da última verificação por privilégios
     * As possibilidades são: config, callback, user, group
     *
     * @return string
     */
    public function getCurrentAbilityOrigin() : string
    {
        return $this->current_ability_origin;
    }

    /**
     * Gera a estrutura de nomeamento de rotas para os CRUDs,
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
    public function normalizeConfig()
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

    /**
     * Devolve as permissões para o usuário na função de acesso especificada
     * O formato procede assim: users.edit = {$role_slug}.edit

     * @param  int $user_id
     * @param  string $role_slug
     * @return Collection
     */
    public function getUserPermissions($user_id, string $role_slug)
    {
        if (session('user.abilities') == null) {

            // Gera um cache de permissões
            // para evitar consultas ao banco de dados

            $cache_all   = [];
            $cache_slugs = [];
            $roles = \Laracl\Models\AclRole::all();
            foreach($roles as $item) {
                $cache_all[$item->slug] = $item->toArray();
                $cache_slugs[$item->id] = $item->slug;
            }

            // As permissões setadas para o usuário tem precedência
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

    /**
     * Registra os verificadores de acesso com base na configuração
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




}
