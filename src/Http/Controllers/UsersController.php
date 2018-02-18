<?php

namespace Laracl\Http\Controllers;

use Laracl\Models\AclUser;
use Laracl\Models\AclGroup;
use Laracl\Models\AclUserPermission;
use Illuminate\Http\Request;
use Gate;
use DB;

class UsersController extends Controller
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
                $query->orWhere('username', 'like', "%{$q}%");
                $query->orWhere('email', 'like', "%{$q}%");
            }
        };
            
        $order   = $request->get('order', 'id');
        $by      = $request->get('by', 'asc');
        $perpage = $request->get('perpage', 10);

        $collection = AclUser::where($filters)
            ->orderBy($order, $by)
            ->paginate($perpage)
            ->appends($request->all());

        $view = config('laracl.views.users.index');

        return view($view)->with([
            'title'             => 'Gerenciamento de Usuários',
            'collection'        => $collection,
            'route_create'      => config('laracl.routes.users.create'),
            'route_edit'        => config('laracl.routes.users.edit'),
            'route_permissions' => config('laracl.routes.users-permissions.edit'),
            'route_groups'      => config('laracl.routes.groups.index'),
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $view = config('laracl.views.users.create');

        return view($view)->with([
            'model'           => new AclUser,
            'groups'          => AclGroup::all(),
            'has_permissions' => false,
            'require_pass'    => 'required',
            'route_index'     => config('laracl.routes.users.index'),
            'route_store'     => config('laracl.routes.users.store'),
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
            'name'         => 'required|max:100',
            'email'        => 'required|unique:users|max:150',
            'password'     => 'required',
        ]);

        // Transforma o password em hash sempre!
        $pass = bcrypt($form->password);
        $form->request->set('password', $pass);

        $model = new AclUser;
        $model->fill($form->all());
        $model->save();

        $route = config('laracl.routes.users.edit');
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
        $db_permissions = AclUserPermission::collectByUser($id);

        $view = config('laracl.views.users.edit');

        return view($view)->with([
            'model'             => AclUser::find($id),
            'groups'            => AclGroup::all(),
            'has_permissions'   => ($db_permissions->count()>0),
            'require_pass'      => '',
            'route_index'       => config('laracl.routes.users.index'),
            'route_update'      => config('laracl.routes.users.update'),
            'route_create'      => config('laracl.routes.users.create'),
            'route_permissions' => config('laracl.routes.users-permissions.edit'),
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
        $model = AclUser::find($id);

        // Se o password for preenchido, transforma em hash
        $pass = $form->request->get('password') == null 
            ? $model->password 
            : bcrypt($form->request->get('password'));
        $form->request->set('password', $pass);

        $form->validate([
            'name'         => 'required|max:100',
            'email'        => "required|unique:users,email,{$id}|max:150",
            'password'     => 'required',
        ]);

        // Usuário possui permissões exclusivas
        if ($form->acl_group_id == 0) {
            $form->request->set('acl_group_id', null);
        }
        // O grupo foi selecionado, 
        // remove as permissões esclusivas
        elseif ($form->acl_group_id != 0 && AclUserPermission::collectByUser($id)->count()>0) {
            $result = AclUserPermission::removeByUser($id);
        }
        
        $model->fill($form->all());
        $model->save();

        return back();
    }
}
