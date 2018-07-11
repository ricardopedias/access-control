<?php
namespace Acl\Tests\Unit\Models;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Models;

class AclUserPermissionTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testPermissions()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);
        $role = self::createRole();
        $permissions = self::createUserPermissions($role->id, $user->id, true, true, true, true);

        $this->assertInstanceOf(Models\AclUser::class, $permissions->user);
        $this->assertInstanceOf(Models\AclRole::class, $permissions->role);
    }
}
