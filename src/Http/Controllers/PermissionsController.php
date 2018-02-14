<?php

namespace Laracl\Http\Controllers;

use Laracl\Models\AclPermission;
use Illuminate\Http\Request;
use Gate;
use DB;

class PermissionsController extends Controller
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
                    'create' => null,
                    'edit'   => null,
                    'show'   => null,
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
        $user_permissions = AclPermission::collectByUser($id);

        $permissions = [];
        foreach ($user_permissions as $item) {
            $permissions[$item->role->slug] = [
                'label'  => $item->role->name,
                'create' => $item->create,
                'edit'   => $item->edit,
                'show'   => $item->show,
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

        return view('permissions.edit')->with([
            'model' => \App\User::find($id),
            'roles' => $this->getRolesStructure(),
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
        foreach ($form->roles as $route => $perms) {

            $model = \App\Permissions::firstOrNew([
                'user_id' => $id,
                'route'   => $route,
                ]);

            $model->fill([
                'create'  => ($perms['create'] ?? 'no'),
                'edit'    => ($perms['edit'] ?? 'no'),
                'show'    => ($perms['show'] ?? 'no'),
                'delete'  => ($perms['delete'] ?? 'no'),
                ]);

            $model->save();
        }

        return back();
    }
}
