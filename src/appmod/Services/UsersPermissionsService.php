<?php
namespace Laracl\Services;

use Illuminate\Http\Request;
use Laracl\Repositories\AclUsersRepository;
use Laracl\Repositories\AclUsersPermissionsRepository;
use Laracl\Repositories\AclRolesRepository;
use Laracl\Models\AclUserPermission;

class UsersPermissionsService implements CrudContract
{
    public function gridList(string $view, Request $request = null)
    {
        // Nâo disponível neste contexto
    }

    public function gridTrash(string $view, Request $request = null)
    {
        // Nâo disponível neste contexto
    }

    public function formCreate(string $view, Request $request = null)
    {
        // Nâo disponível neste contexto
    }

    public function formEdit(string $view, $id, Request $request = null)
    {
        return view($view)->with([
            'user'         => ($user = (new AclUsersRepository)->read($id)),
            'structure'    => $this->getStructure($user->id),
            'route_index'  => config('laracl.routes.users.index'),
            'route_user'   => config('laracl.routes.users.edit'),
            'route_update' => config('laracl.routes.users-permissions.update'),
            'route_groups' => config('laracl.routes.groups.index'),
            'breadcrumb'   => [
                '<i class="fas fa-user"></i> Usuários' => route(config('laracl.routes.users.index')),
                '<i class="fas fa-user"></i> ' . $user->name => route(config('laracl.routes.users.edit'), $user->id),
                'Permissões'
            ]
        ]);
    }

    public function dataInsert(array $data)
    {
        // Nâo disponível neste contexto
    }

    public function dataUpdate(array $data, int $id)
    {
        $results = [];
        foreach ($data['permissions'] as $slug => $perms) {

            $role = (new AclRolesRepository)->findBySlug($slug);

            // Aplica as permissões para o usuário
            $model = AclUserPermission::firstOrNew([
                'user_id' => $id,
                'role_id' => $role->id,
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

    public function dataDelete(array $data, int $id = null)
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
    * @param  int  $user_id
    * @param  bool $allows_null
    * @return array|null
    */
    public function getStructure($user_id, $allows_null = false)
    {
        $permissions = [];

        $collection = (new AclUsersPermissionsRepository)->collectByUserID($user_id);
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
