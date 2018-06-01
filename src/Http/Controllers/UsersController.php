<?php

namespace Laracl\Http\Controllers;

use Illuminate\Http\Request;
use SortableGrid\Http\Controllers\SortableGridController;
use Laracl\Models;
use Laracl\Repositories\AclUsersRepository;

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
     * Devolve a coleção que será usada para a busca.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getSearchableBuilder()
    {
        $columns = [];

        // \App\User
        // Adiciona o prefixo 'users' nos campos do modelo
        $fillable_user = (new Models\AclUser)->getFillableColumns();
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
        $fillable_group = (new Models\AclGroup)->getFillableColumns();
        foreach($fillable_group as $field) {
            $columns[] = "acl_groups.{$field} as group_{$field}";
        }
        $columns[] = "acl_groups.created_at as group_created_at";
        $columns[] = "acl_groups.updated_at as group_updated_at";

        // Faz o select devolvendo os campos de \App\User + \Laracl\Models\AclGroup
        return Models\AclUser::select($columns)
            ->leftJoin('acl_users_groups', 'users.id', '=', 'acl_users_groups.user_id')
            ->leftJoin('acl_groups', 'acl_users_groups.group_id', '=', 'acl_groups.id');
            //->groupBy('users.id');
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
            'model'           => new Models\AclUser,
            'groups'          => Models\AclGroup::all(),
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

        AclUsersRepository::create($form->all());

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
        $db_permissions = Models\AclUserPermission::collectByUser($id);

        $view = config('laracl.views.users.edit');

        return view($view)->with([
            'model'             => Models\AclUser::find($id),
            'groups'            => Models\AclGroup::all(),
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
        $model = Models\AclUser::find($id);

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

        if (empty($form->request->get('acl_group_id')) == true) {
            // Se grupo for setado como 0,
            // remove relacionamentos existentes com grupos
            Models\AclUserGroup::where('user_id', $id)->delete();
        }

        if (empty($form->request->get('acl_group_id')) == false) {
            // Se um grupo for selecionado e o usuário possuir permissões exclusivas,
            // elas serão removidas, pois as permissões do grupo serão usadas no lugar
            Models\AclUserPermission::where('user_id', $id)->delete();
        }

        // Atualiza os dados do usuário
        $model->fill($form->all());
        $model->save();

        if (empty($form->request->get('acl_group_id')) == false) {
            // Um grupo foi selecionado
            $group = Models\AclUserGroup::where('user_id', $id)->first();
            if ($group == null) {
                $group = new Models\AclUserGroup;
                $group->user_id = $id;
            }
            $group->group_id = $form->request->get('acl_group_id');
            $group->save();
        }

        return back();
    }
}
