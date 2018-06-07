<?php

namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use Laracl\Repositories\AclUsersRepository;
use Laracl\Repositories\AclUsersPermissionsRepository;

class UsersPermissionsController extends Controller
{
    /**
     * Exibe o formulário de configuração das permissões de acesso.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $view = config('laracl.views.users-permissions.edit');
        return view($view)->with([
            'user'         => ($user = (new AclUsersRepository)->read($id)),
            'structure'    => (new AclUsersPermissionsRepository)->getStructure($user->id),
            'route_index'  => config('laracl.routes.users.index'),
            'route_user'   => config('laracl.routes.users.edit'),
            'route_update' => config('laracl.routes.users-permissions.update'),
            'route_groups' => config('laracl.routes.groups.index'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                '<i class="fas fa-user"></i> ' . $user->name => route(config('laracl.routes.users.edit'), $user->id),
                'Permissões'
            ]
        ]);
    }

    /**
     * Atualiza as permissões no banco de dados
     *
     * @param  \Illuminate\Http\Request $form
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $form, $id)
    {
        $updated = (new AclUsersPermissionsRepository)->update($id, $form->all());

        $route = config('laracl.routes.users-permissions.edit');
        return redirect()->route($route, $id);
    }
}
