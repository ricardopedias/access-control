<?php

namespace Tests\Unit\Http\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Repositories\AclGroupsRepository;

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

        $this->assertInstanceOf(\Laracl\Models\AclGroup::class, $group);
        $this->assertInstanceOf(\Laracl\Models\AclGroup::class, $model);

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
