<?php
namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use Laracl\Services;

class GroupsController extends Controller
{
    /**
     * Exibe a lista de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $view = config('laracl.views.groups.index');
        return (new Services\GroupsService)->gridList($view);
    }

    /**
     * Exibe a lista de registros na lixeira.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request)
    {
        $view = config('laracl.views.groups.trash');
        return (new Services\GroupsService)->gridTrash($view);
    }

    /**
     * Exibe o formulário para a criação de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $view = config('laracl.views.groups.create');
        return (new Services\GroupsService)->formCreate($view);
    }

    /**
     * Armazena no banco de dados o novo registro criado.
     *
     * @param  \Illuminate\Http\Request $form
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100|unique:acl_groups,name',
        ]);

        $model = (new Services\GroupsService)->dataInsert($request->all());
        $route = config('laracl.routes.groups.index');
        return redirect()->route($route, $model);
    }

    /**
     * Exibe o formulário para edição do registro especificado.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $view = config('laracl.views.groups.edit');
        return (new Services\GroupsService)->formEdit($view, $id);
    }

    /**
     * Atualiza o registro especificado no banco de dados.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => "required|max:100|unique:acl_groups,name,{$id}"
        ]);

        $model = (new Services\GroupsService)->dataUpdate($request->all(), $id);
        return back();
    }

    /**
     * Remove o registro especificado do banco de dados.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $deleted = (new Services\GroupsService)->dataDelete($request->all(), $id);
        return response()->json(['deleted' => $deleted]);
    }
}
