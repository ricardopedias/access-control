<?php

namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use Laracl\Models\AclUser;
use Laracl\Models\AclRole;
use Laracl\Models\AclUserPermission;
use Laracl\Models\AclGroupPermission;
use Laracl\Traits\HasRolesStructure;

class UsersPermissionsController extends Controller
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
        $db_permissions = AclUserPermission::where('user_id', $id)->get();
        $has_permissions = ($db_permissions->count()>0);

        // Se o usuário não possuir permissões específicas
        // popula o formulário com as permissões do grupo
        // para facilitar a vida ;)
        if ($has_permissions == false) {
            $group_relation = AclUser::find($id)->groupRelation;
            if($group_relation != null) {
                $group_id = $group_relation->group_id;
                $db_permissions = AclGroupPermission::where('group_id', $group_id)->get();
            } else {
                $db_permissions = collect([]);
            }
        }

        // Aplica as permissões do banco na estrutura
        // de permissões do formulário
        $this->populateStructure($db_permissions);

        $user = AclUser::find($id);
        $view = config('laracl.views.users-permissions.edit');
        return view($view)->with([
            'title'           => "Permissões Específicas para \"{$user->name}\"",
            'user'            => $user,
            'has_permissions' => $has_permissions,
            'roles'           => $this->getRolesStructure(),
            'route_index'     => config('laracl.routes.users.index'),
            'route_user'      => config('laracl.routes.users.edit'),
            'route_update'    => config('laracl.routes.users-permissions.update'),
            'route_groups'    => config('laracl.routes.groups.index'),
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
            $model = AclUserPermission::firstOrNew([
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
