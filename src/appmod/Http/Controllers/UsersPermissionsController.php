<?php
namespace Acl\Http\Controllers;

use Illuminate\Http\Request;
use Acl\Services;

class UsersPermissionsController extends Controller
{
    /**
     * Exibe o formulário de configuração das permissões de acesso.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        return (new Services\UsersPermissionsService)->formEdit($id, $request);
    }

    /**
     * Atualiza as permissões no banco de dados
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = (new Services\UsersPermissionsService)->dataUpdate($request->all(), $id);
        $route = config('acl.routes.users-permissions.edit');
        return redirect()->route($route, $id)->with('success', 'Permissões atualizadas com sucesso');
    }
}
