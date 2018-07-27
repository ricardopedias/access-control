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
use Acl\Repositories\AclGroupsPermissionsRepository;

class GroupsPermissionsRepositoryTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testCollectByGroupID()
    {
        $this->assertTrue(true);
    }
}
