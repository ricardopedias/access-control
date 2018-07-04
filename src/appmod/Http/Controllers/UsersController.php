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
        return (new Services\UsersService)->gridList($request, $view);
    }
    /**
     * Exibe a lista de registros na lixeira.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request)
    {
        $view = config('laracl.views.users.trash');
        return (new Services\UsersService)->gridTrash($request, $view);
    }

    /**
     * Exibe o formulário para a criação de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $view = config('laracl.views.users.create');
        return (new Services\UsersService)->formCreate($request, $view);
    }

    /**
     * Armazena no banco de dados o novo registro criado.
     *
     * @param  \Illuminate\Http\Request $form
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = (new Services\UsersService)->dataInsert($request);
        $route = config('laracl.routes.users.index');
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
        $view = config('laracl.views.users.edit');
        return (new Services\UsersService)->formEdit($request, $view, $id);
    }

    /**
     * Atualiza os dados de um usuário existente.
     *
     * @param  \Illuminate\Http\Request $form
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $form, $id)
    {
        $model = (new Services\UsersService)->dataUpdate($form, $id);
        return back();
    }

    /**
     * Remove o registro especificado do banco de dados.
     *
     * @param Request $form
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $form, $id)
    {
        $deleted = (new Services\UsersService)->dataDelete($form, $id);
        return response()->json(['deleted' => $deleted]);
    }
}
