<?php
namespace Acl\Repositories;

use Acl\Models\AclUser;
use Acl\Models\AclUserGroup;
use Acl\Models\AclUserPermission;

class AclUsersRepository extends BaseRepository
{
    protected $model_class = AclUser::class;

    protected $soft_deletes = true;
}
