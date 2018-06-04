<?php
namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use SortableGrid\Http\Controllers\SortableGridController;
use Laracl\Repositories\AclUsersRepository;
use Laracl\Repositories\AclGroupsRepository;

class UsersController extends SortableGridController
{
    protected $initial_field = 'users.id';

    protected $initial_order = 'desc';

    protected $initial_perpage = 10;

    protected $fields = [
        'users.id'         => 'ID',
        'users.name'       => 'Nome',
        'acl_groups.name'  => 'Permissões',
        'users.email'      => 'E-mail',
        'users.created_at' => 'Criação',
        'Ações'
    ];

    protected $searchable_fields = [
        'users.id',
        'users.name',
        'users.email',
    ];

    protected $orderly_fields = [
        'users.id',
        'users.name',
        'acl_groups.name',
        'users.email',
        'users.created_at',
    ];

    /**
     * Devolve a instância do builder que será usada para a busca.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getSearchableBuilder()
    {
        return (new AclUsersRepository)->getSearcheable();
    }

    /**
     * Exibe a lista de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $view = config('laracl.views.users.index');
        return $this->searchableView($view)->with([
            'title'             => 'Gerenciamento de Usuários',
            'route_create'      => config('laracl.routes.users.create'),
            'route_edit'        => config('laracl.routes.users.edit'),
            'route_permissions' => config('laracl.routes.users-permissions.edit'),
            'route_groups'      => config('laracl.routes.groups.index'),
            ]);
    }

    /**
     * Exibe o formulário para a criação de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $view = config('laracl.views.users.create');

        return view($view)->with([
            'model'           => (new AclUsersRepository)->read(),
            'groups'          => (new AclGroupsRepository)->getAll(false),
            'title'           => 'Novo Usuário',
            'require_pass'    => 'required',
            'route_index'     => config('laracl.routes.users.index'),
            'route_store'     => config('laracl.routes.users.store'),
            ]);
    }

    /**
     * Armazena no banco de dados o novo registro criado.
     *
     * @param  \Illuminate\Http\Request $form
     * @return \Illuminate\Http\Response
     */
    public function store(Request $form)
    {
        $form->validate([
            'name'         => 'required|max:100',
            'email'        => 'required|unique:users|max:150',
            'password'     => 'required',
        ]);

        $model = (new AclUsersRepository)->create($form->all());

        $route = config('laracl.routes.users.edit');
        return redirect()->route($route, $model);
    }

    /**
     * Exibe o formulário para edição do registro especificado.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $view = config('laracl.views.users.edit');
        return view($view)->with([
            'model'             => (new AclUsersRepository)->read($id),
            'groups'            => (new AclGroupsRepository)->getAll(false),
            'require_pass'      => '',
            'title'             => 'Editar Usuário',
            'route_index'       => config('laracl.routes.users.index'),
            'route_update'      => config('laracl.routes.users.update'),
            'route_create'      => config('laracl.routes.users.create'),
            'route_permissions' => config('laracl.routes.users-permissions.edit'),
            ]);
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
        $form->validate([
            'name'         => 'required|max:100',
            'email'        => "required|unique:users,email,{$id}|max:150"
        ]);

        $updated = (new AclUsersRepository)->update($id, $form->all());

        return back();
    }

    /**
     * Remove o registro especificado do banco de dados.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $updated = (new AclUsersRepository)->delete($id);
        return back();
    }
}
