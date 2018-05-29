<?php

namespace Laracl\Http\Controllers;

use Laracl\Models\AclGroup;
use Laracl\Models\AclRole;
use Laracl\Models\AclGroupPermission;
use Laracl\Traits\HasRolesStructure;
use Illuminate\Http\Request;
use Gate;
use DB;

class GroupsPermissionsController extends Controller
{
    use HasRolesStructure;

    /**
     * Exibe o formulário de configuração das permissões de acesso.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Aplica as permissões do banco na estrutura
        // de permissões do formulário
        $db_permissions = AclGroupPermission::where('group_id', $id);
        $this->populateStructure($db_permissions);

        $group = AclGroup::find($id);
        $view = config('laracl.views.groups-permissions.edit');
        return view($view)->with([
            'title'        => "Permissões para \"{$group->name}\"",
            'group'        => $group,
            'roles'        => $this->getRolesStructure(),
            'route_index'  => config('laracl.routes.groups.index'),
            'route_create' => config('laracl.routes.groups.create'),
            'route_update' => config('laracl.routes.groups-permissions.update'),
            'route_groups' => config('laracl.routes.groups.index'),
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
        foreach ($form->roles as $slug => $perms) {

            $role = $this->getSyncedRole($slug);

            // Aplica as permissões para o grupo
            $model = AclGroupPermission::firstOrNew([
                'role_id' => $role->id,
                'group_id' => $id,
                ]);

            $model->fill([
                'create' => ($perms['create'] ?? 'no'),
                'read'   => ($perms['read'] ?? 'no'),
                'update' => ($perms['update'] ?? 'no'),
                'delete' => ($perms['delete'] ?? 'no'),
                ]);

            $model->save();
        }

        $route = config('laracl.routes.groups-permissions.edit');
        return redirect()->route($route, $id);
    }
}
