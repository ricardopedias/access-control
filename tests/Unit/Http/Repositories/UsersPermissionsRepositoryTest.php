<?php
namespace Tests\Unit\Http\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Repositories\AclUsersPermissionsRepository;

class UsersPermissionsRepositoryTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testCollectByUserID()
    {
        $this->assertTrue(true);
    }
}
