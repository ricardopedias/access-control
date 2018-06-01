<?php
namespace Laracl\Repositories;

use Laracl\Models\AclUser;
use Laracl\Models\AclUserGroup;
use Carbon\Carbon;

class UsersRepository extends IRepository
{
    protected $modelClass = AclUser::class;

    public function create(array $data)
    {
        $result = AclUser::create($data);

        // Se acl_group_id = 0 ou null
        if (isset($data['acl_group_id']) && intval($data['acl_group_id']) > 0) {
            $relation = AclUserGroup::create([
                'user_id'  => $model->id,
                'group_id' => $data['acl_group_id']
            ]);
        }

        return $result;
    }

    public function update($id, array $data)
    {
        $user = AclUser::find($id);
        $user->fill($data);
        return $user->save();;
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
