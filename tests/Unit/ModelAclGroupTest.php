<?php

namespace Laracl\Tests\Unit;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Models;

class ModelAclGroupTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testGroupNoRelations()
    {
        $group = self::createGroup();

        $this->assertInstanceOf(Collection::class, $group->users);
        $this->assertInstanceOf(Collection::class, $group->roles);
        $this->assertInstanceOf(Collection::class, $group->permissions);

        $this->assertCount(0, $group->users);
        $this->assertCount(0, $group->roles);
        $this->assertCount(0, $group->permissions);
    }

    public function testGroupRelations()
    {
        $group = self::createGroup();
        $role = self::createRole();
        $permissions = self::createGroupPermissions($role->id, $group->id, true, true, true, true);

        self::createUser($group->id);
        self::createUser($group->id);
        self::createUser($group->id);

        // Atributos mágicos do modelo
        $this->assertInstanceOf(Collection::class, $group->users);
        $this->assertCount(3, $group->users);
        $this->assertInstanceOf(Models\AclUser::class, $group->users->first());

        $this->assertInstanceOf(Collection::class, $group->roles);
        $this->assertCount(1, $group->roles);
        $this->assertInstanceOf(Models\AclRole::class, $group->roles->first());

        $this->assertInstanceOf(Collection::class, $group->permissions);
        $this->assertCount(1, $group->permissions);
        $this->assertInstanceOf(Models\AclGroupPermission::class, $group->permissions[0]);
    }

    public function testGroupCan()
    {
        $group = self::createGroup();
        $role_one = self::createRole();
        $permissions = self::createGroupPermissions($role_one->id, $group->id, true, true, true, true);

        $role_two = self::createRole();
        $permissions = self::createGroupPermissions($role_two->id, $group->id, false, false, false, false);

        // Atributos mágicos do modelo
        $this->assertInstanceOf(Collection::class, $group->roles);
        $this->assertCount(2, $group->roles);
        $this->assertInstanceOf(Models\AclRole::class, $group->roles->first());
        $this->assertInstanceOf(Models\AclRole::class, $group->roles->where('slug', $role_one->slug)->first());

        $this->assertFalse($group->canCreate(99));
        $this->assertTrue($group->canCreate($role_one->id));
        $this->assertFalse($group->canCreate($role_two->id));

        $this->assertFalse($group->canRead(99));
        $this->assertTrue($group->canRead($role_one->id));
        $this->assertFalse($group->canRead($role_two->id));

        $this->assertFalse($group->canUpdate(99));
        $this->assertTrue($group->canUpdate($role_one->id));
        $this->assertFalse($group->canUpdate($role_two->id));

        $this->assertFalse($group->canDelete(99));
        $this->assertTrue($group->canDelete($role_one->id));
        $this->assertFalse($group->canDelete($role_two->id));
    }
}
