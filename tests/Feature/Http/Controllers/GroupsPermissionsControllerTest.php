<?php
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
