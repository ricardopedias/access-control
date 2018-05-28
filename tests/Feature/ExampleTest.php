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
    public function testBasicTest()
    {
        // $this->withoutMiddleware();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/laracl/users');
        $response->assertStatus(200);

        // $response = $this->get('/laracl/users/create');
        // $response->assertStatus(200);
        //
        // $response = $this->get('/laracl/users/edit');
        // $response->assertStatus(200);

        dd('xxx');
    }
}
