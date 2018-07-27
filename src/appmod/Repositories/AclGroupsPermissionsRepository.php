<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Repositories;

use Acl\Models\AclGroupPermission;
use Acl\Repositories\AclRolesRepository;

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
    public function collectByGroupID($group_id, $take = 0, bool $paginate = false)
    {
        return $this->collectBy('group_id', $group_id, $take, $paginate);
    }
}
