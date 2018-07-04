<?php
namespace Laracl\Services;

use Illuminate\Http\Request;
use Laracl\Repositories\AclUsersRepository;
use Laracl\Repositories\AclGroupsRepository;
use Laracl\Models\AclUserGroup;
use Laracl\Models\AclUserPermission;
use SortableGrid\Traits\HasSortableGrid;

class GroupsService implements CrudContract
{
    use HasSortableGrid;

    public function gridList(Request $request, string $view)
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

        $provider = (new AclGroupsRepository)->newQuery();
        $this->setDataProvider($provider);

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

    public function gridTrash(Request $request, string $view)
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

        $provider = (new AclGroupsRepository)->newQuery()->onlyTrashed();
        $this->setDataProvider($provider);

        return $this->gridView($view)->with([
            'route_create'      => config('laracl.routes.groups.create'),
            'route_edit'        => config('laracl.routes.groups.edit'),
            'route_destroy'     => config('laracl.routes.groups.destroy'),
            'route_permissions' => config('laracl.routes.groups-permissions.edit'),
            'route_trash'       => config('laracl.routes.groups.trash'),
            'route_restore'       => config('laracl.routes.groups.restore'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                '<i class="fas fa-user-friends"></i> Grupos' => route(config('laracl.routes.groups.index')),
                'Lixeira'
            ]
        ]);
    }

    public function formCreate(Request $request, string $view)
    {
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

    public function formEdit(Request $request, string $view, $id)
    {
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

    public function dataInsert(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100|unique:acl_groups,name',
        ]);

        return (new AclGroupsRepository)->create($request->all());
    }

    public function dataUpdate(Request $request, int $id = null)
    {
        $request->validate([
            'name' => "required|max:100|unique:acl_groups,name,{$id}"
        ]);

        return (new AclGroupsRepository)->update($id, $request->all());
    }

    public function dataDelete(Request $request, int $id = null)
    {
        if ($request->request->get('mode') == 'soft') {
            $deleted = (new AclGroupsRepository)->delete($id);
        } else {
            $deleted = (new AclGroupsRepository)->delete($id, true);
        }
        return $deleted;
    }
}
