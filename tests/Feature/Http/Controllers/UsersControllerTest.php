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

class UsersControllerTest extends IControllerTestCase
{
    use RefreshDatabase;

    // Para verificar as rotas disponiveis
    // php artisan route:list

    public function testIndex()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/acl/users');
        $response->assertStatus(200);
    }

    // CREATE
    public function testCreate()
    {
        self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/acl/users/create');
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
        $response = $this->post('/acl/users', $post);
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
            'name'     => $faker->name,
            'email'    => $user_email,
            'password' => bcrypt('secret'),
            'group_id' => $group_id
        ];

        // Requisição POST
        $response = $this->post('/acl/users', $post);

        // Usuário criado
        $user = \App\User::where('email', $user_email)->first();
        $this->assertNotNull($user);
        $this->assertInstanceOf('\App\User', $user);

        $response->assertStatus(302);
        $response->assertRedirect("/acl/users");

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
            'name'     => $faker->name,
            'email'    => $user_email,
            'password' => bcrypt('secret'),
            'group_id' => $group->id
        ];

        // Requisição POST
        $response = $this->post('/acl/users', $post);

        // Usuário criado
        $user = \App\User::where('email', $user_email)->first();
        $this->assertNotNull($user);
        $this->assertInstanceOf('\App\User', $user);

        $response->assertStatus(302);
        $response->assertRedirect("/acl/users");

        // Grupo Relacionado
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $user->id, 'group_id' => $group->id]);
    }

    public function testEdit()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $edit_user = self::createUser();

        $response = $this->get("/acl/users/" . $edit_user->id . "/edit");
        $response->assertStatus(200);
    }

    public function testUpdatePasswordOptional()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            // 'password'     => bcrypt('secret'), // Ausência de campo obrigatório
        ];

        // Requisição PUT
        $original_user = self::createUser();
        $response = $this->put("/acl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/acl/users/" . $original_user->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/acl/users/" . $original_user->id . "/edit");

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
        // Loga o usuário administrador
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
        $original_user = self::createUser();
        $response = $this->put("/acl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/acl/users/" . $original_user->id . "/edit"
        ]);
        $response->assertStatus(302);
        $response->assertRedirect("/acl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // O password é um campo oculto
        // @see https://laravel.com/docs/5.6/eloquent-serialization#hiding-attributes-from-json
        $user = collect(\DB::select('select password from users where id = ' . $original_user->id))->first();
        $this->assertNotEquals($original_user->password, $user->password);
    }

    public function testUpdateAddGroup_NoUserPermissions()
    {
        // Loga o usuário administrador
        $user = \App\User::find(1);
        $this->actingAs($user);

        self::createGroup();
        $group = self::createGroup();
        self::createGroup();

        // Usuário não tem grupo nem permissões de usuário
        $original_user = self::createUser();
        $this->assertDatabaseMissing('acl_users_groups', ['user_id' => $original_user->id]);
        $this->assertDatabaseMissing('acl_users_permissions', ['user_id' => $original_user->id]);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'group_id' => $group->id // Criação de um grupo
        ];
        $response = $this->put("/acl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/acl/users/" . $original_user->id . "/edit"
        ]);
        $response->assertStatus(302);
        $response->assertRedirect("/acl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // Grupo Adicionado
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $edited_user->id, 'group_id' => $group->id]);

        // Apenas um grupo por usuário é permitido
        $this->assertEquals(1, \Acl\Models\AclUserGroup::where('user_id', $edited_user->id)->count());

        // Não há permissões de usuário
        $this->assertDatabaseMissing('acl_users_permissions', ['user_id' => $original_user->id]);
    }

    public function testUpdateChangeGroup_NoUserPermissions()
    {
        // Loga o usuário administrador
        $user = \App\User::find(1);
        $this->actingAs($user);

        $group_one = self::createGroup();
        $group_two = self::createGroup();

        // Usuário tem um grupo mas não permissões de usuário
        $original_user = self::createUser($group_one->id);
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $original_user->id, 'group_id' => $group_one->id]);
        $this->assertDatabaseMissing('acl_users_permissions', ['user_id' => $original_user->id]);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'group_id' => $group_two->id
        ];
        $response = $this->put("/acl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/acl/users/" . $original_user->id . "/edit"
        ]);
        $response->assertStatus(302);
        $response->assertRedirect("/acl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // Grupo Atualizado
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $edited_user->id, 'group_id' => $group_two->id]);
        $this->assertDatabaseMissing('acl_users_groups', ['user_id' => $edited_user->id, 'group_id' => $group_one->id]);

        // Apenas um grupo por usuário é permitido
        $this->assertEquals(1, \Acl\Models\AclUserGroup::where('user_id', $edited_user->id)->count());

        // Não há permissões de usuário
        $this->assertDatabaseMissing('acl_users_permissions', ['user_id' => $original_user->id]);
    }

    public function testUpdateRemoveGroup_NoUserPermissions()
    {
        // Loga o usuário administrador
        $user = \App\User::find(1);
        $this->actingAs($user);

        $group = self::createGroup();
        $original_user = self::createUser($group->id);

        // Usuário tem um grupo
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $original_user->id, 'group_id' => $group->id]);

        // Não possui permissões de usuário
        $this->assertDatabaseMissing('acl_users_permissions', ['user_id' => $original_user->id]);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'group_id' => 0 // Grupo ausente
        ];
        $response = $this->put("/acl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/acl/users/" . $original_user->id . "/edit"
        ]);
        $response->assertStatus(302);
        $response->assertRedirect("/acl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // Grupo Removido
        $this->assertDatabaseMissing('acl_users_groups', ['user_id' => $edited_user->id]);

        // Não há permissões de usuário
        $this->assertDatabaseMissing('acl_users_permissions', ['user_id' => $original_user->id]);
    }

    /**
     * Ao adicionar um grupo para um usuário que possui permissões
     * As permissões específicas de usuário devem ser removidas
     */
    public function testUpdateAddGroup_WithUserPermissions()
    {
        // Loga o usuário administrador
        $user = \App\User::find(1);
        $this->actingAs($user);

        self::createGroup();
        $group = self::createGroup();
        self::createGroup();
        $role = self::createRole();

        $original_user = self::createUser();
        $permissions = self::createUserPermissions($role->id, $original_user->id, true, true, true, true);

        // Usuário não tem grupo
        $this->assertDatabaseMissing('acl_users_groups', ['user_id' => $original_user->id]);
        // Usuário possui permissões de usuário
        $this->assertDatabaseHas('acl_users_permissions', ['user_id' => $original_user->id]);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $put = [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'group_id' => $group->id // Criação de um grupo
        ];
        $response = $this->put("/acl/users/" . $original_user->id, $put, [
            'HTTP_REFERER' => "/acl/users/" . $original_user->id . "/edit"
        ]);
        $response->assertStatus(302);
        $response->assertRedirect("/acl/users/" . $original_user->id . "/edit");

        // Usuário atualizado
        $edited_user = \App\User::find($original_user->id);
        $this->assertNotEquals($original_user->name, $edited_user->name);
        $this->assertNotEquals($original_user->email, $edited_user->email);

        // Grupo Adicionado
        $this->assertDatabaseHas('acl_users_groups', ['user_id' => $edited_user->id, 'group_id' => $group->id]);

        // Apenas um grupo por usuário é permitido
        $this->assertEquals(1, \Acl\Models\AclUserGroup::where('user_id', $edited_user->id)->count());

        // As permissões de usuário foram excluídas
        $this->assertDatabaseMissing('acl_users_permissions', ['user_id' => $original_user->id]);
    }
}
