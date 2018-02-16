<?php

namespace Laracl\Http\Controllers;

use Laracl\Models\AclGroup;
use Laracl\Models\AclRole;
use Laracl\Models\AclGroupPermission;
use Illuminate\Http\Request;
use Gate;
use DB;

class GroupsPermissionsController extends Controller
{
    private $routes = null; 

    /**
     * Este método gera uma lista de opções para o formulário de opções.
     * A fonte de rotas é obtida diretamente do Gate do Laravel.
     * 
     * @return array
     */
    private function getRolesStructure()
    {
        if ($this->routes !== null) {
            return $this->routes;
        }

        $abilities = config('laracl.roles');

        // Habilidades resistradas
        foreach (Gate::abilities() as $ability => $closure) {

            $nodes = explode('.', $ability);
            $route = $nodes[0];
            $role  = $nodes[1];

            if ( !isset($this->routes[$route]) ) {
                $this->routes[$route] = [
                    'label' => $abilities[$route]['label']
                ];
            }

            if ( !isset($this->routes[$route]['roles']) ) {
                $this->routes[$route]['roles'] = [
                    'show'   => null,
                    'create' => null,
                    'edit'   => null,
                    'delete' => null,
                ];
            }

            // Não nulos aparecerão no formulário
            $this->routes[$route]['roles'][$role] = '';
        }

        return $this->routes;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Permissoes do banco
        $group_permissions = AclGroupPermission::collectByGroup($id);

        dd($group_permissions);

        $permissions = [];
        foreach ($group_permissions as $item) {
            $permissions[$item->role->slug] = [
                'show'   => $item->show,
                'create' => $item->create,
                'edit'   => $item->edit,
                'delete' => $item->delete,
            ];
        }

        // Aplica as permissões na estrutura de habilidades
        foreach ($this->getRolesStructure() as $route => $item) {
            foreach ($item['roles'] as $role => $nullable) {
                if ($nullable !== null) {
                    $this->routes[$route]['roles'][$role] = isset($permissions[$route])
                        ? $permissions[$route][$role] : 'no';
                }
            }
        }

        dd($this->getRolesStructure());

        $view = config('laracl.views.groups-permissions.edit');

        return view($view)->with([
            'title'        => config('laracl.name'),
            'group'        => AclGroup::find($id),
            'roles'        => $this->getRolesStructure(),
            'route_index'  => config('laracl.routes.groups.index'),
            'route_create' => config('laracl.routes.groups.create'),
            'route_update' => config('laracl.routes.groups-permissions.update'),
            'route_groups' => config('laracl.routes.groups.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $form
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $form, $id)
    {

        foreach ($form->roles as $slug => $perms) {

            $role = AclRole::findBySlug($slug);

            // Se a função nunca foi setada, 
            // deve ser criada
            if ($role == NULL) {

                $info = config("laracl.roles.{$slug}");
                $role = AclRole::create([
                    'name' => $info['label'],
                    'slug' => $slug,
                ]);
            }

            // Aplica as permissões para o usuário
            $model = AclGroupPermission::firstOrNew([
                'role_id' => $role->id,
                'group_id' => $id,
                ]);

            $model->fill([
                'show'    => ($perms['show'] ?? 'no'),
                'create'  => ($perms['create'] ?? 'no'),
                'edit'    => ($perms['edit'] ?? 'no'),
                'delete'  => ($perms['delete'] ?? 'no'),
                ]);

            $model->save();
        }

        $route = config('laracl.routes.groups-permissions.edit');
        return redirect()->route($route, $id);
    }
}
