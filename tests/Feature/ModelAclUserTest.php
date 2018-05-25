<?php

namespace Laracl\Tests\Feature;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Models;

class ModelAclUserTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testUserNoRelations()
    {
        $user = self::createUser();

        // Atributos mágicos do modelo
        $this->assertNull($user->groupRelation);
        $this->assertNull($user->group);

        $this->assertInstanceOf(Collection::class, $user->permissions);
        $this->assertCount(0, $user->permissions);

        $this->assertInstanceOf(Collection::class, $user->roles);
        $this->assertCount(0, $user->roles);
    }

    public function testUserRelations()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);
        $role = self::createRole();
        $permissions = self::createUserPermissions($role->id, $user->id, true, true, true, true);

        // Atributos mágicos do modelo
        $this->assertInstanceOf(Models\AclUserGroup::class, $user->groupRelation);
        $this->assertInstanceOf(Models\AclGroup::class, $user->groupRelation->group);

        $this->assertInstanceOf(Collection::class, $user->permissions);
        $this->assertCount(1, $user->permissions);
        $this->assertInstanceOf(Models\AclUserPermission::class, $user->permissions[0]);

        $this->assertInstanceOf(Collection::class, $user->roles);
        $this->assertCount(1, $user->roles);
        $this->assertInstanceOf(Models\AclRole::class, $user->roles->first());
    }

    public function testUserCan()
    {
        $user = self::createUser();

        $role_one = self::createRole();
        $permissions = self::createUserPermissions($role_one->id, $user->id, true, true, true, true);

        $role_two = self::createRole();
        $permissions = self::createUserPermissions($role_two->id, $user->id, false, false, false, false);

        // Atributos mágicos do modelo
        $this->assertInstanceOf(Collection::class, $user->roles);
        $this->assertCount(2, $user->roles);
        $this->assertInstanceOf(Models\AclRole::class, $user->roles->first());
        $this->assertInstanceOf(Models\AclRole::class, $user->roles->where('slug', $role_one->slug)->first());

        $this->assertFalse($user->canCreate(99));
        $this->assertTrue($user->canCreate($role_one->id));
        $this->assertFalse($user->canCreate($role_two->id));

        $this->assertFalse($user->canRead(99));
        $this->assertTrue($user->canRead($role_one->id));
        $this->assertFalse($user->canRead($role_two->id));

        $this->assertFalse($user->canUpdate(99));
        $this->assertTrue($user->canUpdate($role_one->id));
        $this->assertFalse($user->canUpdate($role_two->id));

        $this->assertFalse($user->canDelete(99));
        $this->assertTrue($user->canDelete($role_one->id));
        $this->assertFalse($user->canDelete($role_two->id));
    }

    /*public function testFindRoleBySlug()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);

        $role = self::createRole();
        $permissions = self::createUserPermissions($role->id, $user->id, true, true, true, true);

        dd(AclUser::findRoleBySlug())

        $this->assertInstanceOf(Collection::class, $user->permissions);

        $this->assertCount(1, $user->permissions);
        $this->assertInstanceOf(Models\AclUserPermission::class, $user->permissions[0]);

    }*/

    /**
     * A basic test example.
     *
     * @return void
     */
    /*
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
    */
}
