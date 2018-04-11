<?php

namespace Laracl\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Faker;

class ExampleTest extends TestCase
{

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        // Seta o arquivo de configuração para testes
        $config_file = dirname(__DIR__) . '/Files/custom-config.php';
        putenv("LARACL_CONFIG_FILE={$config_file}");
        
        $app = require __DIR__ . '/../../../../../bootstrap/app.php';

        $kernel = $app->make(Kernel::class);

        $kernel->bootstrap();

        return $app;
    }


    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, array_merge(require $path, $config));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $config = config('database');
        dd($config);

        $faker = Faker\Factory::create();

        $user = \Laracl\Models\AclUser::create([
            'name'           => $faker->name,
            'email'          => $faker->unique()->safeEmail,
            'password'       => bcrypt('secret'),
            'remember_token' => str_random(10),
            'acl_group_id'   => 1
        ]);


        // dd($user);

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->get('/laracl/users');

        $this->assertAuthenticated();

        $response = $this->get('laracl/no-exists');
        $response->assertStatus(404);

        $response = $this->get('laracl/users');
        $response->assertStatus(200);

        //$response->assertForbidden();

        // $this->artisan('config:clear');

        // $this->app['laracl']::setConfigFile(dirname(__DIR__) . '/Files/custom-config.php');

        // $this->assertFileExists(dirname(__DIR__) . '/Files/custom-config.php');

        // $this->mergeConfigFrom(dirname(__DIR__) . '/Files/custom-config.php', 'laracl');


        // $config = $this->app['config']->get('laracl');

        // $this->app->boot();
        
        // dd($this->app);
        // dd($this->app['laracl']::getConfigFile());

        // $config = config('laracl');

        // \App::shouldReceive('get')
        //             ->once()
        //             ->with('key')
        //             ->andReturn('value');

        // dd($config);
        
        
    }
}
