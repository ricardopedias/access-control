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

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserCreateTest()
    {
        self::createGroup();

        // $this->withoutMiddleware();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/users');
        $response->assertStatus(200);

        $response = $this->get('/laracl/users/create');
        $response->assertStatus(200);

        $faker = \Faker\Factory::create();
        $user_email = $faker->unique()->safeEmail;
        $response = $this->post('/laracl/users', [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'acl_group_id' => 1
        ]);

        $user = \App\User::where('email', $user_email)->first();

        $response->assertStatus(302);
        $response->assertRedirect("/laracl/users/" . $user->id . "/edit");
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
