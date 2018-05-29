<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IControllerTestCase;

class GroupsControllerTest extends IControllerTestCase
{
    use RefreshDatabase;

    // Para fverifica as rotas disponiveis
    // php artisan route:list

    public function testIndex()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/groups');
        $response->assertStatus(200);
    }

    // CREATE
    public function testCreate()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/groups/create');
        $response->assertStatus(200);
    }

    public function testEdit()
    {
        $group = self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/groups/' . $group->id . '/edit');
        $response->assertStatus(200);
    }
}
