<?php
namespace Laracl\Repositories;

use Laracl\Models\AclUser;
use Laracl\Models\AclUserGroup;
use Laracl\Models\AclUserPermission;

class AclUsersRepository extends BaseRepository
{
    protected $model_class = AclUser::class;
}
