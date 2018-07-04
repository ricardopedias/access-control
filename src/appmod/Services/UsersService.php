<?php
namespace Laracl\Services;

use Illuminate\Http\Request;
use Laracl\Repositories\AclUsersRepository;
use Laracl\Repositories\AclGroupsRepository;
use Laracl\Models\AclUserGroup;
use Laracl\Models\AclUserPermission;
use SortableGrid\Traits\HasSortableGrid;

class UsersService implements CrudContract
{
    use HasSortableGrid;

    public function getSearcheable()
    {
        $columns = [];

        // \App\User
        // Adiciona o prefixo 'users' nos campos do modelo
        $fillable_user = (new AclUsersRepository)->newModel()->getFillableColumns();
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
        $fillable_group = (new AclGroupsRepository)->newModel()->getFillableColumns();
        foreach($fillable_group as $field) {
            $columns[] = "acl_groups.{$field} as group_{$field}";
        }
        $columns[] = "acl_groups.created_at as group_created_at";
        $columns[] = "acl_groups.updated_at as group_updated_at";

        // Faz o select devolvendo os campos de \App\User + \Laracl\Models\AclGroup
        return (new AclUsersRepository)->newQuery()->select($columns)
            ->leftJoin('acl_users_groups', 'users.id', '=', 'acl_users_groups.user_id')
            ->leftJoin('acl_groups', 'acl_users_groups.group_id', '=', 'acl_groups.id');
    }

    public function gridList(string $view, Request $request = null)
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

        $provider = $this->getSearcheable();
        $this->setDataProvider($provider);

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

    public function gridTrash(string $view, Request $request = null)
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

        $provider = (new AclUsersRepository)->getSearcheable()->onlyTrashed();
        $this->setDataProvider($provider);

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

    public function formCreate(string $view, Request $request = null)
    {
        return view($view)->with([
            'model'           => (new AclUsersRepository)->read(),
            'groups'          => (new AclGroupsRepository)->collectAll(),
            'title'           => 'Novo Usuário',
            'require_pass'    => 'required',
            'route_store'     => config('laracl.routes.users.store'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                'Novo Usuário'
            ]
        ]);
    }

    public function formEdit(string $view, $id, Request $request = null)
    {
        return view($view)->with([
            'model'             => ($user = (new AclUsersRepository)->read($id)),
            'groups'            => (new AclGroupsRepository)->collectAll(),
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

    public function dataInsert(array $data)
    {
        $data['password'] = isset($data['password']) && !empty($data['password'])
            ? bcrypt($data['password'])
            : bcrypt(uniqid());

        $model = (new AclUsersRepository)->create($data);

        if (isset($data['group_id']) && !empty($data['group_id'])) {
            // Se acl_group_id for diferente de 0 ou null
            $relation = AclUserGroup::create([
                'user_id'  => $model->id,
                'group_id' => $data['group_id']
            ]);
        }

        return $model;
    }

    public function dataUpdate(array $data, int $id)
    {
        $model = (new AclUsersRepository)->findByID($id);

        // Se o password for preenchido, transforma em hash
        $data['password'] = !isset($data['password']) || empty($data['password'])
            ? $model->password
            : bcrypt($data['password']);

        if (isset($data['group_id'])) {

            if (empty($data['group_id'])) {
                // Se grupo for setado como 0 ou null,
                // remove relacionamentos existentes com grupos
                AclUserGroup::where('user_id', $id)->delete();

            } else {
                // Se um grupo for selecionado e o usuário possuir permissões exclusivas,
                // elas serão removidas, pois as permissões do grupo serão usadas no lugar
                AclUserPermission::where('user_id', $id)->delete();

                $group = AclUserGroup::where('user_id', $id)->first();
                if ($group == null) {
                    $group = new AclUserGroup;
                    $group->user_id = $id;
                }
                $group->group_id = $data['group_id'];
                $group->save();
            }
        }

        // Atualiza os dados do usuário
        $model->fill($data);
        return $model->save();
    }

    public function dataDelete(array $data, int $id = null)
    {
        if (isset($data['mode']) && $data['mode'] == 'soft') {
            $deleted = (new AclUsersRepository)->delete($id);
        } else {
            $deleted = (new AclUsersRepository)->delete($id, true);
        }
        return $deleted;
    }
}
