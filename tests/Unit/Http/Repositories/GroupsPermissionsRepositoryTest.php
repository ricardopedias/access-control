<?php
namespace Tests\Unit\Http\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Repositories\AclGroupsPermissionsRepository;

class GroupsPermissionsRepositoryTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testCollectByGroupID()
    {
        $this->assertTrue(true);
    }
}
