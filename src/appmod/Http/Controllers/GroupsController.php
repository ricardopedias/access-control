<?php
namespace Acl\Http\Controllers;

use Illuminate\Http\Request;
use Acl\Http\Requests\StoreGroupPost;
use Acl\Http\Requests\UpdateGroupPost;
use Acl\Services;

class GroupsController extends Controller
{
    /**
     * Exibe a lista de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return (new Services\GroupsService)->gridList($request);
    }

    /**
     * Exibe a lista de registros na lixeira.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request)
    {
        return (new Services\GroupsService)->gridTrash($request);
    }

    /**
     * Exibe o formulário para a criação de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return (new Services\GroupsService)->formCreate($request);
    }

    /**
     * Armazena no banco de dados o novo registro criado.
     *
     * @param  \Acl\Http\Requests\StoreGroupPost $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGroupPost $request)
    {
        $model = (new Services\GroupsService)->dataInsert($request->all());
        $route = config('acl.routes.groups.index');
        return redirect()->route($route)->with('success', 'Grupo criado com sucesso');
    }

    /**
     * Exibe o formulário para edição do registro especificado.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        return (new Services\GroupsService)->formEdit($id, $request);
    }

    /**
     * Atualiza o registro especificado no banco de dados.
     *
     * @param  \Acl\Http\Requests\UpdateGroupPost $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGroupPost $request, $id)
    {
        $model = (new Services\GroupsService)->dataUpdate($request->all(), $id);
        return back()->with('success', 'Grupo atualizado com sucesso');
    }

    /**
     * Remove o registro especificado do banco de dados.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $deleted = (new Services\GroupsService)->dataDelete($request->all(), $id);
        return response()->json(['deleted' => $deleted]);
    }

    /**
     * Restaura o registro especificado, removendo-o da lixeira.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        $restored = (new Services\GroupsService)->dataRestore($request->all(), $id);
        return response()->json(['restored' => $restored]);
    }
}
