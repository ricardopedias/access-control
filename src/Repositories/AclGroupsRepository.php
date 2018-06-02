<?php
namespace Laracl\Repositories;

use Laracl\Models\AclGroup;
use Laracl\Models\AclUserGroup;
use Carbon\Carbon;

class AclGroupsRepository extends IRepository
{
    protected $model_class = AclGroup::class;

    /**
     * Cria um novo usuário.
     *
     * @param  array  $data
     * @return Laracl\Models\AclUser
     */
    /*public function create(array $data)
    {
        $data['password'] = isset($data['password'])
            ? bcrypt($data['password'])
            : null;

        $model = $this->newQuery()->create($data);

        // Se acl_group_id = 0 ou null
        if (isset($data['acl_group_id']) && intval($data['acl_group_id']) > 0) {
            $relation = AclUserGroup::create([
                'user_id'  => $model->id,
                'group_id' => $data['acl_group_id']
            ]);
        }

        return $model;
    }*/

    /**
     * Atualiza os dados de um usuário existente.
     *
     * @param  int    $id
     * @param  array  $data
     * @return mixed
     */
    /*public function update($id, array $data)
    {
        $model = $this->findByID($id);

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
    }*/
}
