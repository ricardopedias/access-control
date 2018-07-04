<?php
namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use Laracl\Services;

class UsersPermissionsController extends Controller
{
    /**
     * Exibe o formulário de configuração das permissões de acesso.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $view = config('laracl.views.users-permissions.edit');
        return (new Services\UsersPermissionsService)->formEdit($request, $view, $id);
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
        $model = (new Services\UsersPermissionsService)->dataUpdate($form, $id);
        $route = config('laracl.routes.users-permissions.edit');
        return redirect()->route($route, $id);
    }
}
