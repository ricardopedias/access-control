<?php
namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use SortableGrid\Traits\HasSortableGrid;
use Laracl\Repositories\AclUsersRepository;
use Laracl\Repositories\AclGroupsRepository;

class UsersController extends Controller
{
    use HasSortableGrid;

    /**
     * Exibe a lista de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->setInitials('users.id', 'desc', 10);

        $this->addGridField('ID', 'users.id');
        $this->addGridField('Nome', 'users.name');
        $this->addGridField('Permissões', 'acl_groups.name');
        $this->addGridField('E-mail', 'users.email');
        $this->addGridField('Criação', 'users.created_at');
        $this->addGridField('Ações');

        $this->addSearchField('users.id');
        $this->addSearchField('users.name');
        $this->addSearchField('users.email');

        $this->addOrderlyField('users.id');
        $this->addOrderlyField('users.name');
        $this->addOrderlyField('acl_groups.name');
        $this->addOrderlyField('users.email');
        $this->addOrderlyField('users.created_at');

        $this->setDataProvider((new AclUsersRepository)->getSearcheable());

        $view = config('laracl.views.users.index');
        return $this->gridView($view)->with([
            'route_create'      => config('laracl.routes.users.create'),
            'route_edit'        => config('laracl.routes.users.edit'),
            'route_destroy'     => config('laracl.routes.users.destroy'),
            'route_permissions' => config('laracl.routes.users-permissions.edit'),
            'route_groups'      => config('laracl.routes.groups.index'),
            'route_trash'       => config('laracl.routes.users.trash'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários'
            ]
            ]);
    }
    /**
     * Exibe a lista de registros na lixeira.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request)
    {
        $this->setInitials('users.id', 'desc', 10);

        $this->addGridField('ID', 'users.id');
        $this->addGridField('Nome', 'users.name');
        $this->addGridField('Permissões', 'acl_groups.name');
        $this->addGridField('E-mail', 'users.email');
        $this->addGridField('Criação', 'users.created_at');
        $this->addGridField('Ações');

        $this->addSearchField('users.id');
        $this->addSearchField('users.name');
        $this->addSearchField('users.email');

        $this->addOrderlyField('users.id');
        $this->addOrderlyField('users.name');
        $this->addOrderlyField('acl_groups.name');
        $this->addOrderlyField('users.email');
        $this->addOrderlyField('users.created_at');

        $this->setDataProvider((new AclUsersRepository)->getSearcheable());

        $view = config('laracl.views.users.trash');
        return $this->gridView($view)->with([
            'route_create'      => config('laracl.routes.users.create'),
            'route_edit'        => config('laracl.routes.users.edit'),
            'route_destroy'     => config('laracl.routes.users.destroy'),
            'route_permissions' => config('laracl.routes.users-permissions.edit'),
            'route_groups'      => config('laracl.routes.groups.index'),
            'route_trash'       => config('laracl.routes.users.trash'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                'Lixeira'
            ]
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
            'route_store'     => config('laracl.routes.users.store'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                'Novo Usuário'
            ]
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

        $route = config('laracl.routes.users.index');
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
            'model'             => ($user = (new AclUsersRepository)->read($id)),
            'groups'            => (new AclGroupsRepository)->getAll(false),
            'require_pass'      => '',
            'title'             => 'Editar Usuário',
            'route_update'      => config('laracl.routes.users.update'),
            'route_create'      => config('laracl.routes.users.create'),
            'route_permissions' => config('laracl.routes.users-permissions.edit'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                $user->name
            ]
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
     * @param Request $form
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $form, $id)
    {
        if ($form->request->get('mode') == 'soft') {
            $deleted = (new AclUsersRepository)->delete($id);
        } else {
            $deleted = (new AclUsersRepository)->delete($id, true);
        }

        return response()->json(['deleted' => $deleted]);
    }
}
