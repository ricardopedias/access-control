<?php
namespace Acl\Http\Controllers;

use Illuminate\Http\Request;
use Acl\Services;

class UsersController extends Controller
{
    /**
     * Exibe a lista de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return (new Services\UsersService)->gridList($request);
    }
    /**
     * Exibe a lista de registros na lixeira.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request)
    {
        return (new Services\UsersService)->gridTrash($request);
    }

    /**
     * Exibe o formulário para a criação de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return (new Services\UsersService)->formCreate($request);
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
            'name'         => 'required|max:100',
            'email'        => 'required|unique:users|max:150',
            'password'     => 'required',
        ]);

        $model = (new Services\UsersService)->dataInsert($request->all());
        $route = config('acl.routes.users.index');
        return redirect()->route($route)->with('success', "Usuário(a) '{$model->name}' foi criado com sucesso");
    }

    /**
     * Exibe o formulário para edição do registro especificado.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        return (new Services\UsersService)->formEdit($id, $request);
    }

    /**
     * Atualiza os dados de um usuário existente.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|max:100',
            'email'        => "required|unique:users,email,{$id}|max:150"
        ]);

        $model = (new Services\UsersService)->dataUpdate($request->all(), $id);
        return back()->with('success', "Os dados de '{$model->name}' foram atualizados com sucesso");
    }

    /**
     * Remove o registro especificado do banco de dados.
     *
     * @param Request $form
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $deleted = (new Services\UsersService)->dataDelete($request->all(), $id);
        return response()->json(['deleted' => $deleted]);
    }

    /**
     * Restaura o registro especificado, removendo-o da lixeira.
     *
     * @param Request $form
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        $restored = (new Services\UsersService)->dataRestore($request->all(), $id);
        return response()->json(['restored' => $restored]);
    }
}
