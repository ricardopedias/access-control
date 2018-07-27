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
