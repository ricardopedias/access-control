<?php

namespace Laracl\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        // Cria a aplicação e inicia o laravel
        parent::setUp();

        $this->app['config']->set('database.default','sqlite'); 
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        \Artisan::call('migrate');
        \Artisan::call('migrate', ['--path' => 'vendor/plexi/laracl/src/database/migrations']);
    }

    public function tearDown()
    {
        \Artisan::call('migrate:reset', ['--path' => 'vendor/plexi/laracl/src/database/migrations']);
        \Artisan::call('migrate:reset');
    }

    private function createUser($group_id = 1)
    {
        $faker = \Faker\Factory::create();

        return \Laracl\Models\AclUser::create([
            'name'           => $faker->name,
            'email'          => $faker->unique()->safeEmail,
            'password'       => bcrypt('secret'),
            'remember_token' => str_random(10),
            'acl_group_id'   => $group_id
        ]);
    }

    /**
     * No momento da migração, dois grupos padrões são gerados.
     * ID 1 = admin e ID 2 = users, ambos como 'system = yes'
     */
    private function createGroup($group_id = 1)
    {
        $faker = \Faker\Factory::create();

        return \Laracl\Models\AclGroup::create([
            'name' => $faker->name,
            'description' => $faker->paragraph(100),
            'system' => 'no',
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAutentication()
    {
        // Configuração padrão
        $config = config('laracl');
        $this->assertTrue(is_array($config['routes']['users'])); // Já normalizado

        // Funções e Habilidades
        // $this->assertCount(4, $config['roles']);
        $this->assertArrayHasKey('users', $config['roles']);
        $this->assertArrayHasKey('users-permissions', $config['roles']);
        $this->assertArrayHasKey('groups', $config['roles']);
        $this->assertArrayHasKey('groups-permissions', $config['roles']);

        // Rotas        
        $this->assertTrue(is_array($config['routes']['users']));
        $this->assertCount(4, $config['routes']);
        $this->assertArrayHasKey('users', $config['routes']);
        $this->assertArrayHasKey('users-permissions', $config['routes']);
        $this->assertArrayHasKey('groups', $config['routes']);
        $this->assertArrayHasKey('groups-permissions', $config['routes']);

        $url_users = $config['routes']['users']['base'];
        $url_groups = $config['routes']['groups']['base'];


        // Acessar sem login, executa redirecionamento
        // $response = $this->get($url_users);
        // $response->assertStatus(302);


        // Tipo root: (id 1)
        // Grupo: Admin (group_id = 1)
        //$root = $this->createUser(1);

        // Tipo normal: (id 1)
        // Grupo: Admin (group_id = 1)
        //$admin = $this->createUser(1);

        // Tipo normal: (id 1)
        // Grupo: Users (group_id = 2)
        //$common = $this->createUser(2);


        //\Laracl::registerPolicies();


        // $this->actingAs($root)
        //     ->assertAuthenticated('users.show')
        //     ->get($url_users)
        //     ->assertStatus(200);

        // $this->actingAs($admin)
        //     ->get($url_users)
        //     ->assertStatus(200);

        // $this->actingAs($common)
        //     ->get($url_users)
        //     ->assertStatus(200)
        //     ->assertForbidden();

        //$this->assertAuthenticated();

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
