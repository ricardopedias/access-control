<?php
namespace Acl\Http\Controllers;

use Illuminate\Http\Request;
use Acl\Services;

class GroupsPermissionsController extends Controller
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
        return (new Services\GroupsPermissionsService)->formEdit($id, $request);
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
        $model = (new Services\GroupsPermissionsService)->dataUpdate($request->all(), $id);
        $route = config('acl.routes.groups-permissions.edit');
        return redirect()->route($route, $id)->with('success', 'Permissões atualizadas com sucesso');
    }
}
