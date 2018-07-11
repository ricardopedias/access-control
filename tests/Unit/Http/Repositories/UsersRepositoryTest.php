<?php
namespace Tests\Unit\Http\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Repositories\AclUsersRepository;
use Acl\Repositories\AclGroupsRepository;

class UsersRepositoryTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testDefault()
    {
        $this->assertTrue(true);
    }
}
