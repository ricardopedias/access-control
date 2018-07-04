<?php
namespace Laracl\Services;

use Illuminate\Http\Request;
use Laracl\Repositories\AclGroupsRepository;
use Laracl\Repositories\AclGroupsPermissionsRepository;
use Laracl\Repositories\AclRolesRepository;
use Laracl\Models\AclGroupPermission;

class GroupsPermissionsService implements CrudContract
{
    public function gridList(Request $request, string $view)
    {
        // Nâo disponível neste contexto
    }

    public function gridTrash(Request $request, string $view)
    {
        // Nâo disponível neste contexto
    }

    public function formCreate(Request $request, string $view)
    {
        // Nâo disponível neste contexto
    }

    public function formEdit(Request $request, string $view, $id)
    {
        return view($view)->with([
            'group'        => ($group = (new AclGroupsRepository)->read($id)),
            'structure'    => $this->getStructure($group->id),
            'route_index'  => config('laracl.routes.groups.index'),
            'route_create' => config('laracl.routes.groups.create'),
            'route_update' => config('laracl.routes.groups-permissions.update'),
            'route_groups' => config('laracl.routes.groups.index'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                '<i class="fas fa-user-friends"></i> Grupos' => route(config('laracl.routes.groups.index')),
                '<i class="fas fa-user-friends"></i> ' . $group->name => route(config('laracl.routes.groups.edit'), $group->id),
                'Permissões'
            ]
        ]);
    }

    public function dataInsert(Request $request)
    {
        // Nâo disponível neste contexto
    }

    public function dataUpdate(Request $request, int $id = null)
    {
        $data = $request->all();

        $results = [];
        foreach ($data['permissions'] as $slug => $perms) {

            $role = (new AclRolesRepository)->findBySlug($slug);

            // Aplica as permissões para o usuário
            $model = AclGroupPermission::firstOrNew([
                'group_id' => $id,
                'role_id'  => $role->id,
                ]);

            $model->fill([
                'create' => ($perms['create'] ?? 'no'),
                'read'   => ($perms['read'] ?? 'no'),
                'update' => ($perms['update'] ?? 'no'),
                'delete' => ($perms['delete'] ?? 'no'),
                ]);

            $results[] = $model->save();
        }

        $results = \array_unique($results);
        return count($results) == 1 && $results[0] == true;
    }

    public function dataDelete(Request $request, int $id = null)
    {
        // Nâo disponível neste contexto
    }

    /**
     * Este método devolve a estrutura de permissões para
     * a geração do formulário de edição.
     * Se $allows_null for true e o usuário não possuir permissões,
     * o valor null será retornado, caso contrário, uma estrutura
     * com valores desativados será retornada.
     *
     * @param  int  $group_id
     * @param  bool $allows_null
     * @return array|null
     */
    public function getStructure(int $group_id, bool $allows_null = false)
    {
        $permissions = [];

        $collection = (new AclGroupsPermissionsRepository)->collectByGroupID($group_id);
        if ($collection->count() > 0) {
            // Apenas as habilidades do usuário
            foreach ($collection as $item) {
                $permissions[$item->role->slug] = [
                    'create' => $item->create,
                    'read'   => $item->read,
                    'update' => $item->update,
                    'delete' => $item->delete,
                ];
            }
        } elseif($allows_null == true) {
            return null;
        }

        $structure = [];

        $all_abilities = (new RolesService)->getStructure();
        foreach ($all_abilities as $role => $item) {

            foreach ($item['permissions'] as $ability => $nullable) {
                if ($nullable !== null) {
                    $structure[$role]['label'] = $all_abilities[$role]['label'];
                    $structure[$role]['permissions'][$ability] = isset($permissions[$role])
                        ? $permissions[$role][$ability] : 'no';
                }
            }
        }

        return $structure;
    }
}
