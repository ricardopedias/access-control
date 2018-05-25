<?php

namespace Laracl\Tests\Feature;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Models;

class ModelAclUserGroupTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testUserGroupNoRelations()
    {
        $group = self::createGroup();
        $user = self::createUser();

        // Atributos mágicos do modelo
        $this->assertNull($user->groupRelation);
    }

    public function testUserGroupRelations()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);

        // Atributos mágicos do modelo
        $this->assertInstanceOf(Models\AclUserGroup::class, $user->groupRelation);
        $this->assertInstanceOf(Models\AclUser::class, $user->groupRelation->user);
        $this->assertInstanceOf(Models\AclGroup::class, $user->groupRelation->group);
        $this->assertFalse(empty($user->groupRelation->user_id));
        $this->assertFalse(empty($user->groupRelation->group_id));
    }
}
