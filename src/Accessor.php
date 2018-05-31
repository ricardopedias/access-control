<?php

namespace Laracl;

use Gate;

class Accessor
{
    /**
     * Devolve as permissões para o usuário na função de acesso especificada
     * O formato procede assim: users.edit = {$role_slug}.edit

     * @param  int $user_id
     * @param  string $role_slug
     * @return Collection
     */
    /*
    public function getUserPermissions($user_id, string $role_slug)
    {
        return Core::getUserPermissions($user_id, $role_slug);
    }
*/
    /**
     * Verifica se o usuário tem direcito a executar a função de acesso
     * @param  int    $user_id
     * @param  string $role
     * @param  string $permission
     * @param  callable $callback
     * @return bool
     */
    /*
    public function userCan(int $user_id, string $role, string $permission, $callback = null) : bool
    {
        return Core::userCan($user_id, $role, $permission, $callback);
    }
    */
}
