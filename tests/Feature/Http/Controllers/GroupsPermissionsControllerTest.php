<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Acl\Tests\Libs\IControllerTestCase;

class GroupsPermissionsControllerTest extends IControllerTestCase
{
    use RefreshDatabase;

    // Para verificar as rotas disponiveis
    // php artisan route:list

    public function testEdit()
    {
        $group = self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/acl/groups-permissions/' . $group->id . '/edit');
        $response->assertStatus(200);
    }
}
