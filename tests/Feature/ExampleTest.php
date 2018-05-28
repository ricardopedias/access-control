<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IControllerTestCase;


//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends IControllerTestCase
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
        $response = $this->post('/laracl/users/store', [
            'name'         => $faker->name,
            'email'        => $user_email,
            'password'     => bcrypt('secret'),
            'acl_group_id' => 1
        ]);
        $user = \App\User::where('email', $user_email)->first();

        dd($user);


        $response->assertRedirect("/laracl/users/edit/" . $user->id);
        $response->assertStatus(200);
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
