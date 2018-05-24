<?php

namespace Laracl\Tests\Feature;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Models;

class IModelTestCaseTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testAclGroup()
    {
        $model = self::createGroup();
        $this->assertFalse(empty($model->name));
        $this->assertFalse(empty($model->description));
        $this->assertEquals('no', $model->system);
    }

    public function testAclRole()
    {
        $model = self::createRole();

        $this->assertFalse(empty($model->name));
        $this->assertFalse(empty($model->slug));
        $this->assertFalse(empty($model->description));
    }

    public function testAclGroupPermissions()
    {
        $role = self::createRole();
        $group = self::createGroup();
        $model = self::createGroupPermissions($role->id, $group->id, true, true, true, true);
        $this->assertFalse(empty($model->role_id));
        $this->assertFalse(empty($model->group_id));
        $this->assertEquals('yes', $model->create);
        $this->assertEquals('yes', $model->read);
        $this->assertEquals('yes', $model->update);
        $this->assertEquals('yes', $model->delete);

        $group = self::createGroup();
        $model = self::createGroupPermissions($role->id, $group->id, false, false, false, false);
        $this->assertFalse(empty($model->role_id));
        $this->assertFalse(empty($model->group_id));
        $this->assertEquals('no', $model->create);
        $this->assertEquals('no', $model->read);
        $this->assertEquals('no', $model->update);
        $this->assertEquals('no', $model->delete);
    }

    public function testAclUser()
    {
        $model = self::createUser();
        $this->assertFalse(empty($model->name));
        $this->assertFalse(empty($model->email));
        $this->assertFalse(empty($model->password));
    }

    public function testAclUserRelatedWithGroup()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);

        $this->assertFalse(empty($user->name));
        $this->assertFalse(empty($user->email));
        $this->assertFalse(empty($user->password));

        $relation = Models\AclUserGroup::find([$user->id, $group->id]);
        $this->assertEquals($user->id, $relation->user_id);
        $this->assertEquals($group->id, $relation->group_id);
    }

    public function testAclUserPermissions()
    {
        $role = self::createRole();
        $user = self::createUser();
        $model = self::createUserPermissions($role->id, $user->id, true, true, true, true);
        $this->assertFalse(empty($model->role_id));
        $this->assertFalse(empty($model->user_id));
        $this->assertEquals('yes', $model->create);
        $this->assertEquals('yes', $model->read);
        $this->assertEquals('yes', $model->update);
        $this->assertEquals('yes', $model->delete);

        $user = self::createUser();
        $model = self::createUserPermissions($role->id, $user->id, false, false, false, false);
        $this->assertFalse(empty($model->role_id));
        $this->assertFalse(empty($model->user_id));
        $this->assertEquals('no', $model->create);
        $this->assertEquals('no', $model->read);
        $this->assertEquals('no', $model->update);
        $this->assertEquals('no', $model->delete);
    }
}
