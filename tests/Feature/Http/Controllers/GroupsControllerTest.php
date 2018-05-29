<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IControllerTestCase;
use Laracl\Models;

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

    public function testStore()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por POST
        $faker = \Faker\Factory::create();
        $name = $faker->name;
        $post = [
            'name' => $name,
        ];

        // Requisição POST
        $response = $this->post('/laracl/groups', $post);

        // Usuário criado
        $group = Models\AclGroup::where('name', $name)->first();
        $response->assertStatus(302);
        $response->assertRedirect("/laracl/groups/" . $group->id . "/edit");
    }

    public function testEdit()
    {
        $group = self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/groups/' . $group->id . '/edit');
        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $name = $faker->name;
        $put = [
            'name' => $name,
        ];

        // Requisição PUT
        $original_group = self::createGroup();
        $response = $this->put("/laracl/groups/" . $original_group->id, $put, [
            'HTTP_REFERER' => "/laracl/groups/" . $original_group->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/laracl/groups/" . $original_group->id . "/edit");

        // Grupo atualizado
        $edited_group = Models\AclGroup::find($original_group->id);
        $this->assertNotEquals($original_group->name, $edited_group->name);
    }
}
