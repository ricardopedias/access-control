<?php
namespace Laracl\Repositories;

use Laracl\Models\AclGroupPermission;
use Laracl\Repositories\AclRolesRepository;

class AclGroupsPermissionsRepository extends BaseRepository
{
    protected $model_class = AclGroupPermission::class;

    /**
     * Devolve todos os registros.
     * Se $take for false então devolve todos os registros
     * Se $paginate for true retorna uma instânca do Paginator
     *
     * @param  int  $group_id
     * @param  bool $take
     * @param  bool $paginate
     * @return EloquentCollection|Paginator
     */
    public function collectByGroupID(int $group_id, $take = 0, bool $paginate = false)
    {
        return $this->collectBy('group_id', $group_id, $take, $paginate);
    }
}
