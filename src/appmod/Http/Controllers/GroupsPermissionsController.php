<?php
namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use Laracl\Repositories\AclGroupsRepository;
use Laracl\Repositories\AclGroupsPermissionsRepository;

class GroupsPermissionsController extends Controller
{
    /**
     * Exibe o formulário de configuração das permissões de acesso.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $view = config('laracl.views.groups-permissions.edit');
        return view($view)->with([
            'group'        => ($group = (new AclGroupsRepository)->read($id)),
            'structure'    => (new AclGroupsPermissionsRepository)->getStructure($group->id),
            'route_index'  => config('laracl.routes.groups.index'),
            'route_create' => config('laracl.routes.groups.create'),
            'route_update' => config('laracl.routes.groups-permissions.update'),
            'route_groups' => config('laracl.routes.groups.index'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                '<i class="fas fa-user-friends"></i> Grupos' => route(config('laracl.routes.groups.index')),
                '<i class="fas fa-user-friends"></i> ' . $group->name => route(config('laracl.routes.groups.edit'), $group->id),
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
        $updated = (new AclGroupsPermissionsRepository)->update($id, $form->all());

        $route = config('laracl.routes.groups-permissions.edit');
        return redirect()->route($route, $id);
    }
}
