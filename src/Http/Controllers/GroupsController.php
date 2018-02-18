<?php

namespace Laracl\Http\Controllers;

use Laracl\Models\AclGroup;
use Illuminate\Http\Request;
use Gate;
use DB;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response 
     */
    public function index(Request $request)
    {
        $filters = function ($query) use ($request) {

            $q = $request->get('q', NULL);
            if ($q !== NULL) {
                $query->where('name', 'like', "%{$q}%");
            }
        };
            
        $order   = $request->get('order', 'id');
        $by      = $request->get('by', 'asc');
        $perpage = $request->get('perpage', 10);

        $collection = AclGroup::where($filters)
            ->orderBy($order, $by)
            ->paginate($perpage)
            ->appends($request->all());

        $view = config('laracl.views.groups.index');

        return view($view)->with([
            'title'             => 'Grupos de Acesso',
            'collection'        => $collection,
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
