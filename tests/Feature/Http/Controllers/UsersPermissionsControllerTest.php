<?php
namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Acl\Tests\Libs\IControllerTestCase;

class UsersPermissionsControllerTest extends IControllerTestCase
{
    use RefreshDatabase;

    // Para verificar as rotas disponiveis
    // php artisan route:list

    public function testEdit()
    {
        self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/acl/users-permissions/' . $user->id . '/edit');
        $response->assertStatus(200);
    }

    public function testUpdateWithoutGroup()
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

       $this->assertTrue(true);
    }

    public function testUpdateWithGroup()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);
        $this->assertTrue(true);
    }
}
