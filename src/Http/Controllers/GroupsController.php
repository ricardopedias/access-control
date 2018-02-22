<?php

namespace Laracl\Http\Controllers;

use Laracl\Models\AclGroup;
use Illuminate\Http\Request;
use SortableGrid\Http\Controllers\SortableGridController;

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
     * Devolve a coleção que será usada para a busca.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getSearchableCollection()
    {
        return AclGroup::query();
    }

    /**
     * Display a listing of the resource. 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $view = config('laracl.views.groups.index');
        return $this->searchableView($view)->with([
            'title'             => 'Grupos de Acesso',
            'route_create'      => config('laracl.routes.groups.create'),
            'route_edit'        => config('laracl.routes.groups.edit'),
            'route_permissions' => config('laracl.routes.groups-permissions.edit'),
            'route_users'       => config('laracl.routes.users.index'),
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $view = config('laracl.views.groups.create');

        return view($view)->with([
            'model'       => new AclGroup,
            'title'       => 'Novo Grupo de Acesso',
            'route_index' => config('laracl.routes.groups.index'),
            'route_store' => config('laracl.routes.groups.store'),
            'route_users' => config('laracl.routes.users.index'),
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $form
     * @return \Illuminate\Http\Response
     */
    public function store(Request $form)
    {
        $form->validate([
            'name' => 'required|max:100'
        ]);

        $model = new AclGroup;
        $model->fill($form->all());
        $model->save();

        $route = config('laracl.routes.groups.edit');
        return redirect()->route($route, $model);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $view = config('laracl.views.groups.edit');

        return view($view)->with([
            'model'             => AclGroup::find($id),
            'title'             => 'Editar Grupo de Acesso',
            'route_index'       => config('laracl.routes.groups.index'),
            'route_update'      => config('laracl.routes.groups.update'),
            'route_create'      => config('laracl.routes.groups.create'),
            'route_permissions' => config('laracl.routes.groups-permissions.edit'),
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $form
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $form, $id)
    {
        $form->validate([
            'name'         => 'required|max:100'
        ]);

        $model = AclGroup::find($id);
        $model->fill($form->all());
        $model->save();

        return back();
    }
}
