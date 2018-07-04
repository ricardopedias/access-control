<?php
namespace Laracl\Repositories;

use Laracl\Models\AclUserPermission;
use Laracl\Repositories\AclRolesRepository;

class AclUsersPermissionsRepository extends BaseRepository
{
    protected $model_class = AclUserPermission::class;

    /**
     * Devolve todos os registros.
     * Se $take for false então devolve todos os registros
     * Se $paginate for true retorna uma instânca do Paginator
     *
     * @param  int  $user_id
     * @param  int $take
     * @param  bool $paginate
     * @return EloquentCollection|Paginator
     */
    public function collectByUserID(int $user_id, $take = 0, bool $paginate = false)
    {
        return $this->collectBy('user_id', $user_id, $take, $paginate);
    }
}
