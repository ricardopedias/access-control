<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Repositories;

use Acl\Models\AclUserPermission;
use Acl\Repositories\AclRolesRepository;

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
    public function collectByUserID($user_id, $take = 0, bool $paginate = false)
    {
        return $this->collectBy('user_id', $user_id, $take, $paginate);
    }
}
