<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Repositories;

use Acl\Models\AclGroup;

class AclGroupsRepository extends BaseRepository
{
    protected $model_class = AclGroup::class;

    protected $soft_deletes = true;

    /**
     * Devolve um registro com base no seu relacionamento com o usuário
     * Se $failable for true, falhas vão disparar ModelNotFoundException.
     *
     * @param  int  $user_id
     * @param  bool $failable
     *
     * @return Model
     */
    public function findByUserID($user_id, bool $failable = false)
    {
        $builder = $this->newQuery()->select(['acl_groups.*'])
            ->join('acl_users_groups', 'acl_groups.id', '=', 'acl_users_groups.group_id')
            ->where('acl_users_groups.user_id', $user_id);

        if ($failable == true) {
            return $builder->firstOrFail();
        } else {
            return $builder->first();
        }
    }
}
