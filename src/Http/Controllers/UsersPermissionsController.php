<?php

namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use Laracl\Models;

class UsersPermissionsController extends IPermissionsController
{
    /**
     * Exibe o formulário de configuração das permissões de acesso.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Models\AclUser::find($id);
        $group_label = null;

        $user_permissions = Models\AclUserPermission::where('user_id', $id)->get();
        $has_user_permissions = ($user_permissions->count()>0);
        if($has_user_permissions == true) {
            // Usuário possui permissões exclusivas
            // preeche o formulário com elas
            $this->populateStructure($user_permissions);

        } else {

            dd($user->groupRelation);

            // O usuário não possui permissões exclusivas
            // tenta preecher o formulário com as permissões do grupo
            if ($user->groupRelation != null) {

                $group_id = $user->groupRelation->group_id;
                $group_permissions = Models\AclGroupPermission::where('group_id', $group_id)->get();
                dd($group_permissions->first());
                $group_label = $group_permissions->first()->group->label;

                $this->populateStructure($group_permissions);

            } else {
                $this->populateStructure([]);
            }
        }

        $view = config('laracl.views.users-permissions.edit');
        return view($view)->with([
            'title'                => "Permissões Específicas para \"{$user->name}\"",
            'user'                 => $user,
            'group_label'          => $group_label,
            'roles'                => $this->getRolesStructure(),
            'route_index'          => config('laracl.routes.users.index'),
            'route_user'           => config('laracl.routes.users.edit'),
            'route_update'         => config('laracl.routes.users-permissions.update'),
            'route_groups'         => config('laracl.routes.groups.index'),
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

            // Aplica as permissões para o usuário
            $model = Models\AclUserPermission::firstOrNew([
                'user_id' => $id,
                'role_id' => $role->id,
                ]);

            $model->fill([
                'create' => ($perms['create'] ?? 'no'),
                'read'   => ($perms['read'] ?? 'no'),
                'update' => ($perms['update'] ?? 'no'),
                'delete' => ($perms['delete'] ?? 'no'),
                ]);

            $model->save();
        }

        $route = config('laracl.routes.users-permissions.edit');
        return redirect()->route($route, $id);
    }
}
