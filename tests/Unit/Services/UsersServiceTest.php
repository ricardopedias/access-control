<?php
namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Repositories\AclUsersRepository;
use Laracl\Repositories\AclGroupsRepository;
use Laracl\Services\UsersService;

class UsersServiceTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testGetSearcheable()
    {
        $group_one = self::createGroup();
        $group_two = self::createGroup();
        $inserteds = [];
        for ($x=1; $x<=10; $x++) {
            $rand_values = [$group_one->id, $group_two->id, null];
            $group_id    = $rand_values[mt_rand(0, count($rand_values) - 1)];
            $user_model  = self::createUser($group_id);
            $inserteds[$user_model->id] = $user_model;
        }

        $collection = (new UsersService)->getSearcheable()->get();
        // Usuário inicial + 10 adicionados neste teste
        $this->assertCount(1 + 10, $collection);

        foreach ($collection as $item) {
            $this->assertInstanceOf(\Laracl\Models\AclUser::class, $item);
        }
    }

    public function testCreateFull()
    {
        $group = self::createGroup();

        $data = [
            'name'     => self::faker()->name,
            'email'    => self::faker()->unique()->safeEmail,
            'password' => 'secret',
            'group_id' => $group->id
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password);
        $this->assertTrue(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertEquals($group->id, $group_saved->id);
        $this->assertEquals($group->name, $group_saved->name);
        $this->assertEquals($group->description, $group_saved->description);
        $this->assertEquals($group->system, $group_saved->system);
        $this->assertEquals($group->updatet_at, $group_saved->updatet_at);
        $this->assertEquals($group->created_at, $group_saved->created_at);
    }

    public function testCreate_WithoutGroup()
    {
        $data = [
            'name'     => self::faker()->name,
            'email'    => self::faker()->unique()->safeEmail,
            'password' => 'secret',
            //'group_id' => $group->id
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password);
        $this->assertTrue(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertNull($group_saved);
    }

    public function testCreateFull_GroupNull()
    {
        $data = [
            'name'     => self::faker()->name,
            'email'    => self::faker()->unique()->safeEmail,
            'password' => 'secret',
            'group_id' => null
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password);
        $this->assertTrue(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertNull($group_saved);
    }

    public function testCreateFull_WithoutPassword()
    {
        $group = self::createGroup();

        $data = [
            'name'       => self::faker()->name,
            'email'      => self::faker()->unique()->safeEmail,
            //'password' => 'secret',
            'group_id'   => $group->id
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password); // um password qualquer foi usado
        $this->assertFalse(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertEquals($group->id, $group_saved->id);
        $this->assertEquals($group->name, $group_saved->name);
        $this->assertEquals($group->description, $group_saved->description);
        $this->assertEquals($group->system, $group_saved->system);
        $this->assertEquals($group->updatet_at, $group_saved->updatet_at);
        $this->assertEquals($group->created_at, $group_saved->created_at);
    }

    public function testCreateFull_PasswordNull()
    {
        $group = self::createGroup();

        $data = [
            'name'     => self::faker()->name,
            'email'    => self::faker()->unique()->safeEmail,
            'password' => null,
            'group_id' => $group->id
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password); // um password qualquer foi usado
        $this->assertFalse(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertEquals($group->id, $group_saved->id);
        $this->assertEquals($group->name, $group_saved->name);
        $this->assertEquals($group->description, $group_saved->description);
        $this->assertEquals($group->system, $group_saved->system);
        $this->assertEquals($group->updatet_at, $group_saved->updatet_at);
        $this->assertEquals($group->created_at, $group_saved->created_at);
    }

    public function testRootUserCan()
    {
        $root_user = config('laracl.root_user');
        $this->assertEquals($root_user, 1);

        $role_one = self::createRole();
        self::createUserPermissions($role_one->id, $root_user, true, true, true, true);

        $role_two = self::createRole();
        self::createUserPermissions($role_two->id, $root_user, false, false, false, false);

        foreach (['create', 'read', 'update', 'delete'] as $permission) {

            // Usuário root sempre tem acesso true

            // Função de acesso 1
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertTrue((new UsersService)->userCan($root_user, $role_one->slug, $permission));
            $this->assertEquals('config', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_one->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);

            // Função de acesso 2
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertTrue((new UsersService)->userCan($root_user, $role_two->slug, $permission));
            $this->assertEquals('config', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_two->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);
        }
    }

    public function testUserCanWithUserPermissions()
    {
        $group      = self::createGroup();
        $user       = self::createUser($group->id);

        $role_one   = self::createRole();
        self::createUserPermissions($role_one->id, $user->id, true, true, true, true);

        $role_two   = self::createRole();
        self::createUserPermissions($role_two->id, $user->id, false, false, false, false);

        $role_three = self::createRole();
        // A função de acesso 3 não possui permissões atreladas

        foreach (['create', 'read', 'update', 'delete'] as $permission) {

            // Função de acesso 1
            // Todas as permissões são true
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertTrue((new UsersService)->userCan($user->id, $role_one->slug, $permission));
            $this->assertEquals('user', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_one->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);

            // Função de acesso 2
            // Todas as permissões são false
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse((new UsersService)->userCan($user->id, $role_two->slug, $permission));
            $this->assertEquals('user', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_two->slug,
                'permission' => $permission,
                'granted'    => false,
            ]);

            // Função de acesso 3
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse((new UsersService)->userCan($user->id, $role_three->slug, $permission));
            $this->assertNull(\Laracl\Core::getDebug('current_ability_origin'));
        }
    }

    public function testUserCanWithGroupPermissions()
    {
        $group      = self::createGroup();
        $user       = self::createUser($group->id);

        $role_one   = self::createRole();
        self::createGroupPermissions($role_one->id, $group->id, true, true, true, true);

        $role_two   = self::createRole();
        self::createGroupPermissions($role_two->id, $group->id, false, false, false, false);

        $role_three = self::createRole();
        // A função de acesso 3 não possui permissões atreladas

        foreach (['create', 'read', 'update', 'delete'] as $permission) {

            // Função de acesso 1
            // Todas as permissões são true
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertTrue((new UsersService)->userCan($user->id, $role_one->slug, $permission));
            $this->assertEquals('group', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_one->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);

            // Função de acesso 2
            // Todas as permissões são false
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse((new UsersService)->userCan($user->id, $role_two->slug, $permission));
            $this->assertEquals('group', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_two->slug,
                'permission' => $permission,
                'granted'    => false,
            ]);

            // Função de acesso 3
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse((new UsersService)->userCan($user->id, $role_three->slug, $permission));
            $this->assertNull(\Laracl\Core::getDebug('current_ability_origin'));
        }
    }

    public function testCalbackCan()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);
        $role_one = self::createRole();
        $role_two = self::createRole();

        // As duas funções de acesso deverão retornar true
        // para a rotina chegar até a verificação adicional via callback
        self::createUserPermissions($role_one->id, $user->id, true, true, true, true);
        self::createUserPermissions($role_two->id, $user->id, true, true, true, true);

        foreach (['create', 'read', 'update', 'delete'] as $permission) {

            $this->assertTrue((new UsersService)->userCan($user->id, $role_one->slug, $permission, function(){ return true; }));
            $this->assertTrue((new UsersService)->userCan($user->id, $role_two->slug, $permission, function(){ return true; }));

            $this->assertFalse((new UsersService)->userCan($user->id, $role_one->slug, $permission, function(){ return false; }));
            $this->assertFalse((new UsersService)->userCan($user->id, $role_two->slug, $permission, function(){ return false; }));
        };
    }
}
