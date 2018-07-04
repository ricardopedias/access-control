<?php
namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IControllerTestCase;

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

        $response = $this->get('/laracl/groups-permissions/' . $group->id . '/edit');
        $response->assertStatus(200);
    }
}
