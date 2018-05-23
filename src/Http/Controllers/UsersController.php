<?php

namespace Laracl\Http\Controllers;

use Laracl\Models\AclUser;
use Laracl\Models\AclGroup;
use Laracl\Models\AclUserGroup;
use Laracl\Models\AclUserPermission;
use SortableGrid\Http\Controllers\SortableGridController;
use Illuminate\Http\Request;

class UsersController extends SortableGridController
{
    protected $initial_field = 'users.id';

    protected $initial_order = 'desc';

    protected $initial_perpage = 10;

    protected $fields = [
        'users.id'         => 'ID',
        'users.name'       => 'Nome',
        'acl_groups.name'  => 'Grupo de Acesso',
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
     * Devolve a coleção que será usada para a busca.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getSearchableBuilder()
    {
        $columns = [];

        // \App\User
        // Adiciona o prefixo 'users' nos campos do modelo
        $fillable_user = (new AclUser)->getFillableColumns();
        foreach($fillable_user as $field) {
            $columns["users.{$field}"] = "users.{$field}";
        }

        // Se os campos especiais não forem 'fillable'
        if (!isset($columns['users.id'])) {
            $columns[] = 'users.id';
        }
        if (!isset($columns['users.created_at'])) {
            $columns[] = 'users.created_at';
        }
        if (!isset($columns['users.updated_at'])) {
            $columns[] = 'users.updated_at';
        }

        // \Laracl\Models\AclUser
        // O campo com o grupo de acesso
        $fillable_group = (new AclGroup)->getFillableColumns();
        foreach($fillable_group as $field) {
            $columns[] = "acl_groups.{$field} as group_{$field}";
        }
        $columns[] = "acl_groups.created_at as group_created_at";
        $columns[] = "acl_groups.updated_at as group_updated_at";

        // Faz o select devolvendo os campos de \App\User + \Laracl\Models\AclGroup
        return AclUser::select($columns)
            ->leftJoin('acl_users_groups', 'users.id', '=', 'acl_users_groups.user_id')
            ->leftJoin('acl_groups', 'acl_users_groups.group_id', '=', 'acl_groups.id');
    }

    /**
     * Display a listing of the resource.
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
            'title'           => 'Novo Usuário',
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

        $relation = new AclUserGroup;

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
            'title'             => 'Editar Usuário',
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

        if($form->acl_group_id != 0) {
            AclUserGroup::updateOrCreate(
                ['group_id' => $form->acl_group_id],
                ['user_id' => $id]
            );
        }

        return back();
    }
}
