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

    public function gridList(Request $request, string $view)
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

        $provider = (new AclUsersRepository)->getSearcheable();
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

    public function gridTrash(Request $request, string $view)
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

    public function formCreate(Request $request, string $view)
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

    public function formEdit(Request $request, string $view, $id)
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

    public function dataInsert(Request $request)
    {
        $request->validate([
            'name'         => 'required|max:100',
            'email'        => 'required|unique:users|max:150',
            'password'     => 'required',
        ]);

        $data = $request->all();
        $data['password'] = isset($data['password']) && !empty($data['password'])
            ? bcrypt($data['password'])
            : bcrypt(uniqid());

        $model = (new AclUsersRepository)->create($data);

        if (isset($data['acl_group_id']) && !empty($data['acl_group_id'])) {
            // Se acl_group_id for diferente de 0 ou null
            $relation = (new AclGroupsRepository)->create([
                'user_id'  => $model->id,
                'group_id' => $data['acl_group_id']
            ]);
        }

        return $model;
    }

    public function dataUpdate(Request $request, int $id = null)
    {
        $request->validate([
            'name'         => 'required|max:100',
            'email'        => "required|unique:users,email,{$id}|max:150"
        ]);

        $model = (new AclUsersRepository)->findByID($id);
        $data = $request->all();

        // Se o password for preenchido, transforma em hash
        $data['password'] = !isset($data['password']) || empty($data['password'])
            ? $model->password
            : bcrypt($data['password']);

        if (isset($data['acl_group_id'])) {

            if (empty($data['acl_group_id'])) {
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
                $group->group_id = $data['acl_group_id'];
                $group->save();
            }
        }

        // Atualiza os dados do usuário
        $model->fill($data);
        return $model->save();
    }

    public function dataDelete(Request $request, int $id = null)
    {
        if ($request->request->get('mode') == 'soft') {
            $deleted = (new AclUsersRepository)->delete($id);
        } else {
            $deleted = (new AclUsersRepository)->delete($id, true);
        }
        return $deleted;
    }
}
