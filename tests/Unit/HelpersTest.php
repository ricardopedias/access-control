<?php

namespace Laracl\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;

/**
 * @see https://github.com/sebastianbergmann/phpunit-documentation-brazilian-portuguese/blob/master/src/assertions.rst
 */
class HelpersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        //$app = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/bootstrap/app.php';
        $app = require __DIR__ . '/../../../../../bootstrap/app.php';

        //$app->register(\Laracl\ServiceProvider::class);
        $app->make(Kernel::class)->bootstrap();
        Hash::driver('bcrypt')->setRounds(4);

        return $app;
    }

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

    public function testNormalizeConfig()
    {
        $this->refreshApplication();

        $default_config = __DIR__ . '/../../src/config/laracl.php';        
        $this->assertFileExists($default_config);
        $default_config = require $default_config;


        $custom_config = __DIR__ . '/../Files/custom-config.php';
        $this->assertFileExists($custom_config);
        $custom_config = require $custom_config;

        // Configuração normalizada pelo provider
        $config = config('laracl');
        $this->assertNotEquals($config, $default_config);

        // ----------------------

        // Configuração padrão sem normalizar
        config(['laracl' => $default_config]);
        $config = config('laracl');
        $this->assertEquals($config, $default_config);
        $this->assertTrue(!is_array($config['routes']['users']));
        
        // Normalização
        $this->assertTrue(\Laracl::normalizeConfig());
        $this->assertFalse(\Laracl::normalizeConfig()); // Só normaliza se necessário

        $config = config('laracl');
        $this->assertNotEquals($config, $default_config);
        $this->assertTrue(is_array($config['routes']['users']));
        $this->assertCount(4, $config['routes']);
        $this->assertArrayHasKey('users', $config['routes']);
        $this->assertArrayHasKey('users-permissions', $config['routes']);
        $this->assertArrayHasKey('groups', $config['routes']);
        $this->assertArrayHasKey('groups-permissions', $config['routes']);
        //$this->assertCount(4, $config['roles']);
        $this->assertArrayHasKey('users', $config['roles']);
        $this->assertArrayHasKey('users-permissions', $config['roles']);
        $this->assertArrayHasKey('groups', $config['roles']);
        $this->assertArrayHasKey('groups-permissions', $config['roles']);

        // ----------------------

        // Configuração personalizada sem normalizar
        config(['laracl' => $custom_config]);
        $config = config('laracl');
        $this->assertEquals($config, $custom_config);
        $this->assertTrue(!is_array($config['routes']['users']));

        // Normalização
        $this->assertTrue(\Laracl::normalizeConfig());
        $this->assertFalse(\Laracl::normalizeConfig()); // Só normaliza se necessário

        $config = config('laracl');
        $this->assertNotEquals($config, $default_config);
        $this->assertTrue(is_array($config['routes']['users']));
        $this->assertCount(4, $config['routes']);
        $this->assertArrayHasKey('users', $config['routes']);
        $this->assertArrayHasKey('users-permissions', $config['routes']);
        $this->assertArrayHasKey('groups', $config['routes']);
        $this->assertArrayHasKey('groups-permissions', $config['routes']);
        $this->assertCount(9, $config['roles']);
        $this->assertArrayHasKey('users', $config['roles']);
        $this->assertArrayHasKey('users-permissions', $config['roles']);
        $this->assertArrayHasKey('groups', $config['roles']);
        $this->assertArrayHasKey('groups-permissions', $config['roles']);
        $this->assertArrayHasKey('media', $config['roles']);
        $this->assertArrayHasKey('gallery', $config['roles']);
        $this->assertArrayHasKey('tag', $config['roles']);
        $this->assertArrayHasKey('category', $config['roles']);
        $this->assertArrayHasKey('posts', $config['roles']);
    }

    public function testGetUserPermissions()
    {
        $user = $this->createUser(1);

        $perms = \Laracl::getUserPermissions($user->id, 'users');

        dd($perms);

    }
}
