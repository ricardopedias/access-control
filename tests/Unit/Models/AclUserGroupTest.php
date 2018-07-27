<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Tests\Unit\Models;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Models;

class AclUserGroupTest extends IModelTestCase
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
