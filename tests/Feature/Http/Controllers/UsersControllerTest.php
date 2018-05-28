<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IControllerTestCase;

class UsersControllerTest extends IControllerTestCase
{
    use RefreshDatabase;

    // Para fverifica as rotas disponiveis
    // php artisan route:list

    public function testIndex()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/users');
        $response->assertStatus(200);
    }

    // CREATE
    public function testCreate()
    {
        self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/users/create');
        $response->assertStatus(200);
    }

    public function testStorePasswordRequired()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por POST
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $post = [
            'name'         => $faker->name,
            'email'        => $user_email,
            // 'password'     => bcrypt('secret'), // Ausência de campo obrigatório
        ];

        // Requisição POST
        $response = $this->post('/laracl/users', $post);
        $response->assertSessionHasErrors(['password']);

        $errors = session('errors');
        $this->assertEquals($errors->get('password')[0], "The password field is required.");
    }

    public function testStoreNoGroup()
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

    public function testStoreWithGroup()
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

    public function testEdit()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $edit_user = self::createUser();

        $response = $this->get("/laracl/users/" . $edit_user->id . "/edit");
        $response->assertStatus(200);
    }

    public function testUpdatePasswordOptional()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por POST
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            // 'password'     => bcrypt('secret'), // Ausência de campo obrigatório
        ];

        // Requisição POST
        $original_user = self::createUser();
        $response = $this->put("/laracl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/laracl/users/" . $original_user->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // O password é um campo oculto
        // @see https://laravel.com/docs/5.6/eloquent-serialization#hiding-attributes-from-json
        $user = collect(\DB::select('select password from users where id = ' . $original_user->id))->first();
        $this->assertEquals($original_user->password, $user->password);
    }

    public function testUpdatePassword()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'), // Ausência de campo obrigatório
        ];

        // Requisição PUT
        $original_user = self::createUser();
        $response = $this->put("/laracl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/laracl/users/" . $original_user->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // O password é um campo oculto
        // @see https://laravel.com/docs/5.6/eloquent-serialization#hiding-attributes-from-json
        $user = collect(\DB::select('select password from users where id = ' . $original_user->id))->first();
        $this->assertNotEquals($original_user->password, $user->password);
    }

    public function testUpdateWithGroupNoRelations()
    {
        self::createGroup();
        $group = self::createGroup();
        self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'acl_group_id' => $group->id
        ];

        $original_user = self::createUser();
        // Não existe um relacionamento ainda
        $this->assertDatabaseMissing('acl_users_groups', ['user_id' => $original_user->id, 'group_id' => $group->id]);

        // Requisição PUT
        $response = $this->put("/laracl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/laracl/users/" . $original_user->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // Grupo Relacionado
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $edited_user->id, 'group_id' => $group->id]);
    }

    public function testUpdateWithGroupWithRelations()
    {
        $group_one = self::createGroup();
        $group_two = self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'acl_group_id' => $group_two->id
        ];

        $original_user = self::createUser($group_one->id);
        // Já existe um relacionamento
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $original_user->id, 'group_id' => $group_one->id]);

        // Requisição PUT
        $response = $this->put("/laracl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/laracl/users/" . $original_user->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // Novo grupo relacionado
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $edited_user->id, 'group_id' => $group_two->id]);
    }

    public function testUpdateNoGroupNoRelations()
    {
        self::createGroup();
        $group = self::createGroup();
        self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'acl_group_id' => 0
        ];

        $original_user = self::createUser();
        // Não existe um relacionamento ainda
        $this->assertDatabaseMissing('acl_users_groups', ['user_id' => $original_user->id, 'group_id' => $group->id]);

        // Requisição PUT
        $response = $this->put("/laracl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/laracl/users/" . $original_user->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // Grupo Relacionado
        $this->assertDatabaseMissing('acl_users_groups', ['user_id' => $edited_user->id, 'group_id' => $group->id]);
    }

    public function testUpdateNoGroupWithRelations()
    {
        $group_one = self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'acl_group_id' => 0
        ];

        $original_user = self::createUser($group_one->id);
        // Já existe um relacionamento
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $original_user->id, 'group_id' => $group_one->id]);

        // Requisição PUT
        $response = $this->put("/laracl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/laracl/users/" . $original_user->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // Novo grupo relacionado
        $this->assertDatabaseMissing('acl_users_groups', ['user_id' => $edited_user->id, 'group_id' => $group_one->id]);
    }

    public function testUpdateWithPermissions()
    {
        $user_logged = \App\User::find(1);
        $this->actingAs($user_logged);

        $group = self::createGroup();
        $user = self::createUser($group->id);
        $role = self::createRole();
        $permissions = self::createUserPermissions($role->id, $user->id, true, true, true, true);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'acl_group_id' => 0
        ];

        // Existem permissões exclusivas
        $this->assertDatabaseHas('acl_users_permissions', ['role_id' => $role->id, 'user_id' => $user->id]);

        // Requisição PUT
        $response = $this->put("/laracl/users/" . $user->id, $put, [
            'HTTP_REFERER' => "/laracl/users/" . $user->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($user->id);
        $this->assertNotEquals($user->name, $edited_user->name);
        $this->assertNotEquals($user->email, $edited_user->email);

        // Novo grupo relacionado
        $this->assertDatabaseMissing('acl_users_permissions', ['user_id' => $user->id]);
    }
}
