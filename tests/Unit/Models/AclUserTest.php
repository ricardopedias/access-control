<?php

namespace Laracl\Tests\Unit\Models;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Models;

class AclUserTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testUserNoRelations()
    {
        $user = self::createUser();

        // Atributos mágicos do modelo
        $this->assertNull($user->groupRelation);
        $this->assertNull($user->group);

        $this->assertInstanceOf(Collection::class, $user->permissions);
        $this->assertCount(0, $user->permissions);

        $this->assertInstanceOf(Collection::class, $user->roles);
        $this->assertCount(0, $user->roles);
    }

    public function testUserRelations()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);
        $role = self::createRole();
        $permissions = self::createUserPermissions($role->id, $user->id, true, true, true, true);

        // Atributos mágicos do modelo
        $this->assertInstanceOf(Models\AclUserGroup::class, $user->groupRelation);
        $this->assertInstanceOf(Models\AclGroup::class, $user->groupRelation->group);

        $this->assertInstanceOf(Collection::class, $user->permissions);
        $this->assertCount(1, $user->permissions);
        $this->assertInstanceOf(Models\AclUserPermission::class, $user->permissions[0]);

        $this->assertInstanceOf(Collection::class, $user->roles);
        $this->assertCount(1, $user->roles);
        $this->assertInstanceOf(Models\AclRole::class, $user->roles->first());
    }

    public function testUserCan()
    {
        $user = self::createUser();

        $role_one = self::createRole();
        $permissions = self::createUserPermissions($role_one->id, $user->id, true, true, true, true);

        $role_two = self::createRole();
        $permissions = self::createUserPermissions($role_two->id, $user->id, false, false, false, false);

        // Atributos mágicos do modelo
        $this->assertInstanceOf(Collection::class, $user->roles);
        $this->assertCount(2, $user->roles);
        $this->assertInstanceOf(Models\AclRole::class, $user->roles->first());
        $this->assertInstanceOf(Models\AclRole::class, $user->roles->where('slug', $role_one->slug)->first());

        $this->assertFalse($user->canCreate(99));
        $this->assertTrue($user->canCreate($role_one->id));
        $this->assertFalse($user->canCreate($role_two->id));

        $this->assertFalse($user->canRead(99));
        $this->assertTrue($user->canRead($role_one->id));
        $this->assertFalse($user->canRead($role_two->id));

        $this->assertFalse($user->canUpdate(99));
        $this->assertTrue($user->canUpdate($role_one->id));
        $this->assertFalse($user->canUpdate($role_two->id));

        $this->assertFalse($user->canDelete(99));
        $this->assertTrue($user->canDelete($role_one->id));
        $this->assertFalse($user->canDelete($role_two->id));
    }
}
