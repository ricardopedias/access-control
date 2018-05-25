<?php

namespace Laracl\Tests\Libs;

use Tests\TestCase;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Laracl\Models;
use Illuminate\Support\Str;

class IModelTestCase extends TestCase
{
    public function setUp()
    {
        // Cria a aplicação e inicia o laravel
        parent::setUp();

        $path = explode('\\', get_called_class());
        $class = array_pop($path);
        $data_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $class . ".sqlite";
        exec("touch " . $data_file);

        $this->app['config']->set('database.connections.sqlite.driver', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', $data_file);

        $this->app['config']->set('database.default','sqlite');

        \Artisan::call('migrate');
        \Artisan::call('migrate', ['--path' => 'vendor/plexi/laracl/src/database/migrations']);
    }

    public function tearDown()
    {
        \Artisan::call('migrate:reset', ['--path' => 'vendor/plexi/laracl/src/database/migrations']);
        \Artisan::call('migrate:reset');
    }

    /**
     * No momento da migração, dois grupos padrões são gerados.
     * ID 1 = admin e ID 2 = users, ambos como 'system = yes'
     */
    protected static function createGroup()
    {
        $faker = \Faker\Factory::create();

        return Models\AclGroup::create([
            'name' => $faker->name,
            'description' => $faker->paragraph(30),
            'system' => 'no',
        ]);
    }

    protected static function createGroupPermissions($role_id, $group_id, $create = true, $read = true, $update = true, $delete = true)
    {
        $faker = \Faker\Factory::create();

        return Models\AclGroupPermission::create([
            'role_id'     => $role_id,
            'group_id'    => $group_id,
            'create'      => ($create==true ? 'yes' : 'no'),
            'read'        => ($read==true ? 'yes' : 'no'),
            'update'      => ($update==true ? 'yes' : 'no'),
            'delete'      => ($delete==true ? 'yes' : 'no'),
        ]);
    }

    protected static function createRole()
    {
        $faker = \Faker\Factory::create();
        $name = $faker->name;
        return Models\AclRole::create([
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $faker->paragraph(30),
        ]);
    }

    protected static function createUser($group_id = null)
    {
        $faker = \Faker\Factory::create();

        $user = Models\AclUser::create([
            'name'           => $faker->name,
            'email'          => $faker->unique()->safeEmail,
            'password'       => bcrypt('secret'),
            'remember_token' => str_random(10),
        ]);

        // Relacionamento
        if ($group_id != null) {
            Models\AclUserGroup::create([
                'user_id'  => $user->id,
                'group_id' => $group_id,
            ]);
        }

        return $user;
    }

    protected static function createUserPermissions($role_id, $user_id, $create = true, $read = true, $update = true, $delete = true)
    {
        $faker = \Faker\Factory::create();

        return Models\AclUserPermission::create([
            'role_id'     => $role_id,
            'user_id'     => $user_id,
            'create'      => ($create==true ? 'yes' : 'no'),
            'read'        => ($read==true ? 'yes' : 'no'),
            'update'      => ($update==true ? 'yes' : 'no'),
            'delete'      => ($delete==true ? 'yes' : 'no'),
        ]);
    }
}
