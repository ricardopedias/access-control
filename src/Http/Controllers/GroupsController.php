<?php
namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use SortableGrid\Http\Controllers\SortableGridController;
use Laracl\Repositories\AclGroupsRepository;

class GroupsController extends SortableGridController
{
    protected $initial_field = 'id';

    protected $initial_order = 'desc';

    protected $initial_perpage = 10;

    protected $fields = [
        'id'         => 'ID',
        'name'       => 'Nome',
        'created_at' => 'Criação',
        'Ações'
    ];

    protected $searchable_fields = [
        'id',
        'name',
    ];

    protected $orderly_fields = [
        'id',
        'name',
        'created_at',
    ];

    /**
     * Devolve a instância do builder que será usada para a busca.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getSearchableBuilder()
    {
        return (new AclGroupsRepository)->newQuery();
    }

    /**
     * Exibe a lista de registros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $view = config('laracl.views.groups.index');
        return $this->searchableView($view)->with([
            'route_create'      => config('laracl.routes.groups.create'),
            'route_edit'        => config('laracl.routes.groups.edit'),
            'route_destroy'     => config('laracl.routes.groups.destroy'),
            'route_permissions' => config('laracl.routes.groups-permissions.edit'),
            'route_users'       => config('laracl.routes.users.index'),
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
            'route_index' => config('laracl.routes.groups.index'),
            'route_store' => config('laracl.routes.groups.store'),
            'route_users' => config('laracl.routes.users.index'),
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
            'model'             => (new AclGroupsRepository)->read($id),
            'title'             => 'Editar Grupo de Acesso',
            'route_index'       => config('laracl.routes.groups.index'),
            'route_update'      => config('laracl.routes.groups.update'),
            'route_create'      => config('laracl.routes.groups.create'),
            'route_permissions' => config('laracl.routes.groups-permissions.edit'),
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
