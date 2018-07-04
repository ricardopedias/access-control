<?php
namespace Tests\Unit\Http\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Repositories\AclUsersRepository;
use Laracl\Repositories\AclGroupsRepository;

class UsersRepositoryTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testDefault()
    {
        $this->assertTrue(true);
    }
}
