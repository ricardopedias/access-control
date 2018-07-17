<?php
namespace Acl\Services;

use Illuminate\Http\Request;
use Acl\Repositories\AclGroupsRepository;
use Acl\Repositories\AclGroupsPermissionsRepository;
use Acl\Repositories\AclRolesRepository;
use Acl\Models\AclGroupPermission;

class GroupsPermissionsService implements EditPermissionsContract
{
    public function formEdit(string $view, $id, Request $request = null)
    {
        return view($view)->with([
            'group'        => ($group = (new AclGroupsRepository)->read($id)),
            'structure'    => $this->getStructure($group->id, true),
            'route_index'  => config('acl.routes.groups.index'),
            'route_create' => config('acl.routes.groups.create'),
            'route_update' => config('acl.routes.groups-permissions.update'),
            'route_groups' => config('acl.routes.groups.index'),
            'breadcrumb'        => [
                '<i class="fas fa-user"></i> Usuários' => route(config('acl.routes.users.index')),
                '<i class="fas fa-user-friends"></i> Grupos' => route(config('acl.routes.groups.index')),
                'Grupo "' . $group->name . '"' => route(config('acl.routes.groups.edit'), $group->id),
                'Permissões'
            ]
        ]);
    }

    public function dataUpdate(array $data, int $id)
    {
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

    /**
     * Este método devolve a estrutura de permissões para
     * a geração do formulário de edição.
     * Se $allows_null for true e o usuário não possuir permissões,
     * o valor null será retornado, caso contrário, uma estrutura
     * com valores desativados será retornada.
     *
     * @param  int  $id
     * @param  bool $allows_null
     * @return array|null
     */
    public function getStructure(int $id, bool $allows_null = false)
    {
        $permissions = [];

        $collection = (new AclGroupsPermissionsRepository)->collectByGroupID($id);
        if ($collection->count() > 0) {
            // Apenas as habilidades do grupo
            foreach ($collection as $item) {
                $permissions[$item->role->slug] = [
                    'create' => $item->create,
                    'read'   => $item->read,
                    'update' => $item->update,
                    'delete' => $item->delete,
                ];
            }
        }

        $structure = [];

        $all_abilities = (new RolesService)->getStructure();
        foreach ($all_abilities as $role => $item) {
            // Todas as habilidades disponiveis
            // no arquivo de coniguração ('permissions' => 'create,read,update,delete')
            foreach ($item['permissions'] as $ability => $nullable) {
                if ($nullable !== null) {
                    $structure[$role]['label'] = $all_abilities[$role]['label'];
                    $structure[$role]['permissions'][$ability] = isset($permissions[$role])
                        ? $permissions[$role][$ability] : 'no';
                } elseif($allows_null == true) {
                    // Nulos disponiveis para o formulário
                    $structure[$role]['permissions'][$ability] = null;
                }
            }
        }

        return $structure;
    }
}
