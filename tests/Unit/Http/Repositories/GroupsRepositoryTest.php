<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Tests\Unit\Http\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Repositories\AclGroupsRepository;

class GroupsRepositoryTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testFindByUserID()
    {
        // Preparação
        $group = self::createGroup();
        $user = self::createUser($group->id);

        // Verificações
        $model = (new AclGroupsRepository)->findByUserID($user->id);

        $this->assertInstanceOf(\Acl\Models\AclGroup::class, $group);
        $this->assertInstanceOf(\Acl\Models\AclGroup::class, $model);

        $this->assertEquals($group->id, $model->id);
        $this->assertEquals($group->name, $model->name);
        $this->assertEquals($group->description, $model->description);
        $this->assertEquals($group->system, $model->system);
        $this->assertEquals($group->updatet_at, $model->updatet_at);
        $this->assertEquals($group->created_at, $model->created_at);
    }

    public function testFindByUserID_NotFound()
    {
        $model = (new AclGroupsRepository)->findByUserID(99);
        $this->assertNull($model);
    }

    public function testFindByUserID_NotFoundFailed()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $model = (new AclGroupsRepository)->findByUserID(99, true);
    }
}
