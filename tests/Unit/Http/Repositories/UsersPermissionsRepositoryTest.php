<?php
namespace Tests\Unit\Http\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Repositories\AclUsersPermissionsRepository;

class UsersPermissionsRepositoryTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testCollectByUserID()
    {
        $this->assertTrue(true);
    }
}
