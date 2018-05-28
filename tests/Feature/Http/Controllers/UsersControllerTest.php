<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IControllerTestCase;


//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsersControllerTest extends IControllerTestCase
{
    use RefreshDatabase;

    public function testUserIndexTest()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/users');
        $response->assertStatus(200);
    }

    public function testUserCreateTest()
    {
        self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/users/create');
        $response->assertStatus(200);
    }

    public function testUserStoreNoGroupTest()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $group_id = 0;

        // Dados enviados por POST
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $post = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'acl_group_id' => $group_id
        ];

        // Requisição POST
        $response = $this->post('/laracl/users', $post);

        // Usuário criado
        $user = \App\User::where('email', $user_email)->first();
        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $user->id . "/edit");

        // Grupo Relacionado
        $this->assertDatabaseMissing('acl_users_groups', ['user_id' => $user->id, 'group_id' => $group_id]);
    }

    public function testUserStoreGroupTest()
    {
        self::createGroup();
        $group = self::createGroup();
        self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por POST
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $post = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'acl_group_id' => $group->id
        ];

        // Requisição POST
        $response = $this->post('/laracl/users', $post);

        // Usuário criado
        $user = \App\User::where('email', $user_email)->first();
        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $user->id . "/edit");

        // Grupo Relacionado
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $user->id, 'group_id' => $group->id]);
    }

    public function stopTest()
    {
        dd('xxx');
    }

    public function testUserEditTest()
    {
        // $this->withoutMiddleware();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/users');
        $response->assertStatus(200);

        $response = $this->get('/laracl/users/create');
        $response->assertStatus(200);

        /*$response = $this->get('/laracl/users/edit', [ 'id' => 1]);
        $response->assertStatus(200);*/

        //dd('xxx');
    }
}
