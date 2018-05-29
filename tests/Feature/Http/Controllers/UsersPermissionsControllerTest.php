<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IControllerTestCase;

class UsersPermissionsControllerTest extends IControllerTestCase
{
    use RefreshDatabase;

    // Para fverifica as rotas disponiveis
    // php artisan route:list

    public function testEdit()
    {
        self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/users-permissions/' . $user->id . '/edit');
        $response->assertStatus(200);
    }
}
