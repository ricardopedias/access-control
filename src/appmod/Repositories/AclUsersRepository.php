<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Repositories;

use Acl\Models\AclUser;
use Acl\Models\AclUserGroup;
use Acl\Models\AclUserPermission;

class AclUsersRepository extends BaseRepository
{
    protected $model_class = AclUser::class;

    protected $soft_deletes = true;
}
