<?php
namespace Acl\Tests\Unit\Models;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Models;

class AclRoleTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testRoleNoRelations()
    {
        $role = self::createRole();

        $this->assertInstanceOf(Collection::class, $role->users);
        $this->assertInstanceOf(Collection::class, $role->groups);
        $this->assertInstanceOf(Collection::class, $role->usersPermissions);
        $this->assertInstanceOf(Collection::class, $role->groupsPermissions);

        $this->assertCount(0, $role->users);
        $this->assertCount(0, $role->groups);
        $this->assertCount(0, $role->usersPermissions);
        $this->assertCount(0, $role->groupsPermissions);
    }

    public function testRoleRelations()
    {
        $group_one = self::createGroup();
        $group_two = self::createGroup();

        $user_one = self::createUser($group_one->id);
        $user_two = self::createUser($group_two->id);

        $role = self::createRole();

        self::createUserPermissions($role->id, $user_one->id, true, true, true, true);
        self::createUserPermissions($role->id, $user_two->id, false, false, false, false);

        self::createGroupPermissions($role->id, $group_one->id, true, true, true, true);
        self::createGroupPermissions($role->id, $group_two->id, false, false, false, false);



        // Atributos mágicos do modelo

        $this->assertInstanceOf(Collection::class, $role->users);
        $this->assertCount(2, $role->users);
        $this->assertInstanceOf(Models\AclUser::class, $role->users->first());

        $this->assertInstanceOf(Collection::class, $role->groups);
        $this->assertCount(2, $role->groups);
        $this->assertInstanceOf(Models\AclGroup::class, $role->groups->first());

        $this->assertInstanceOf(Collection::class, $role->usersPermissions);
        $this->assertCount(2, $role->usersPermissions);
        $this->assertInstanceOf(Models\AclUserPermission::class, $role->usersPermissions->first());

        $this->assertInstanceOf(Collection::class, $role->groupsPermissions);
        $this->assertCount(2, $role->groupsPermissions);
        $this->assertInstanceOf(Models\AclGroupPermission::class, $role->groupsPermissions->first());
    }
}
