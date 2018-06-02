<?php
namespace Laracl\Repositories;

use Laracl\Models\AclUser;
use Laracl\Models\AclUserGroup;
use Laracl\Models\AclUserPermission;
use Carbon\Carbon;

class AclUsersRepository extends IRepository
{
    protected $model_class = AclUser::class;

    /**
     * Cria um novo usuário.
     *
     * @param  array  $data
     * @return Laracl\Models\AclUser
     */
    public function create(array $data)
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
    }

    /**
     * Atualiza os dados de um usuário existente.
     *
     * @param  int    $id
     * @param  array  $data
     * @return mixed
     */
    public function update($id, array $data)
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
    }

    public function getAllSearcheable()
    {
        $columns = [];

        // \App\User
        // Adiciona o prefixo 'users' nos campos do modelo
        $fillable_user = $this->newModel()->getFillableColumns();
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
        return $this->newQuery()->select($columns)
            ->leftJoin('acl_users_groups', 'users.id', '=', 'acl_users_groups.user_id')
            ->leftJoin('acl_groups', 'acl_users_groups.group_id', '=', 'acl_groups.id');
    }


    public function getPaying($limit = 15, $paginate = true)
    {

        $now = Carbon::now();
        $query = $this->newQuery();
        $query->where('is_subscriber', true);
        $query->where('subscription_ends_in', '<=', $now);
        $query->orderBy('name');

        return $this->doQuery($query, $limit, $paginate);
    }
}
