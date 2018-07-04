<?php
namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use Laracl\Services;

class GroupsPermissionsController extends Controller
{
    /**
     * Exibe o formulário de configuração das permissões de acesso.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $view = config('laracl.views.groups-permissions.edit');
        return (new Services\GroupsPermissionsService)->formEdit($view, $id);
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
        $route = config('laracl.routes.groups-permissions.edit');
        return redirect()->route($route, $id);
    }
}
