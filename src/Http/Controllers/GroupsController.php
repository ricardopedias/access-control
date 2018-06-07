<?php
namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use SortableGrid\Traits\HasSortableGrid;
use Laracl\Repositories\AclGroupsRepository;

class GroupsController extends Controller
{
    use HasSortableGrid;

    /**
     * Exibe a lista de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->setInitials('id', 'desc', 10);

        $this->addGridField('ID', 'id');
        $this->addGridField('Nome', 'name');
        $this->addGridField('Criação', 'created_at');
        $this->addGridField('Ações');

        $this->addSearchField('id');
        $this->addSearchField('name');

        $this->addOrderlyField('id');
        $this->addOrderlyField('name');
        $this->addOrderlyField('created_at');

        $this->setDataProvider((new AclGroupsRepository)->newQuery());

        $view = config('laracl.views.groups.index');
        return $this->gridView($view)->with([
            'route_create'      => config('laracl.routes.groups.create'),
            'route_edit'        => config('laracl.routes.groups.edit'),
            'route_destroy'     => config('laracl.routes.groups.destroy'),
            'route_permissions' => config('laracl.routes.groups-permissions.edit'),
            'route_trash'       => config('laracl.routes.groups.trash'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                'Grupos'
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
        $view = config('laracl.views.groups.create');

        return view($view)->with([
            'model'       => (new AclGroupsRepository)->read(),
            'title'       => 'Novo Grupo de Acesso',
            'route_store' => config('laracl.routes.groups.store'),
            'route_users' => config('laracl.routes.users.index'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                '<i class="fas fa-user-friends"></i> Grupos' => route(config('laracl.routes.groups.index')),
                'Novo Grupo'
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
            'name' => 'required|max:100|unique:acl_groups,name',
        ]);

        $model = (new AclGroupsRepository)->create($form->all());

        $route = config('laracl.routes.groups.index');
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
        $view = config('laracl.views.groups.edit');

        return view($view)->with([
            'model'             => ($group = (new AclGroupsRepository)->read($id)),
            'title'             => 'Editar Grupo de Acesso',
            'route_update'      => config('laracl.routes.groups.update'),
            'route_create'      => config('laracl.routes.groups.create'),
            'route_permissions' => config('laracl.routes.groups-permissions.edit'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                '<i class="fas fa-user-friends"></i> Grupos' => route(config('laracl.routes.groups.index')),
                $group->name
            ]
        ]);
    }

    /**
     * Atualiza o registro especificado no banco de dados.
     *
     * @param  \Illuminate\Http\Request $form
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $form, $id)
    {
        $form->validate([
            'name' => "required|max:100|unique:acl_groups,name,{$id}"
        ]);

        $updated = (new AclGroupsRepository)->update($id, $form->all());

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
            $deleted = (new AclGroupsRepository)->delete($id);
        } else {
            $deleted = (new AclGroupsRepository)->delete($id, true);
        }

        return response()->json(['deleted' => $deleted]);
    }
}
