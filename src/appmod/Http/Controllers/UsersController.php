<?php
namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use Laracl\Services;

class UsersController extends Controller
{
    /**
     * Exibe a lista de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $view = config('laracl.views.users.index');
        return (new Services\UsersService)->gridList($view, $request);
    }
    /**
     * Exibe a lista de registros na lixeira.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request)
    {
        $view = config('laracl.views.users.trash');
        return (new Services\UsersService)->gridTrash($view, $request);
    }

    /**
     * Exibe o formulário para a criação de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $view = config('laracl.views.users.create');
        return (new Services\UsersService)->formCreate($view);
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
        $route = config('laracl.routes.users.index');
        return redirect()->route($route);
    }

    /**
     * Exibe o formulário para edição do registro especificado.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $view = config('laracl.views.users.edit');
        return (new Services\UsersService)->formEdit($view, $id);
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
        return back();
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
