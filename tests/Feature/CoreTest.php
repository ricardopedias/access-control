<?php

namespace Laracl\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;
use Laracl\Tests\Libs\IModelTestCase;
use Illuminate\Database\Eloquent\Collection;

class CoreTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testeDebug()
    {
        \Laracl\Core::setDebug('test_param', 'test_value');

        // Primeira chamada devolve e exclui
        $debug = \Laracl\Core::getDebug('test_param');
        $this->assertEquals($debug, 'test_value');
        // Debug excluído
        $this->assertNull(\Laracl\Core::getDebug('test_param'));
    }

    public function testeNormalizeConfig()
    {
        // Sobrescreve a configuração para ela voltar ao normal
        // pois \Laracl\Core::normalizeConfig() foi chamado no ServiceProvider
        config([
            'laracl.routes.users'              => 'laracl/users',
            'laracl.routes.users-permissions'  => 'laracl/users-permissions',
            'laracl.routes.groups'             => 'painel/groups', // rota personalizada
            'laracl.routes.groups-permissions' => 'laracl/groups-permissions',
        ]);

        $config = config('laracl');
        $this->assertArrayHasKey('users', $config['routes']);
        $this->assertArrayHasKey('users-permissions', $config['routes']);
        $this->assertArrayHasKey('groups', $config['routes']);
        $this->assertArrayHasKey('groups-permissions', $config['routes']);
        $this->assertTrue(is_string($config['routes']['users']));
        $this->assertTrue(is_string($config['routes']['users-permissions']));
        $this->assertTrue(is_string($config['routes']['groups']));
        $this->assertTrue(is_string($config['routes']['groups-permissions']));

        // Realiza o processo de normalização
        $this->assertTrue(\Laracl\Core::normalizeConfig());

        $config = config('laracl');
        $this->assertArrayHasKey('users', $config['routes']);
        $this->assertArrayHasKey('users-permissions', $config['routes']);
        $this->assertArrayHasKey('groups', $config['routes']);
        $this->assertArrayHasKey('groups-permissions', $config['routes']);
        $this->assertTrue(is_array($config['routes']['users']));
        $this->assertTrue(is_array($config['routes']['users-permissions']));
        $this->assertTrue(is_array($config['routes']['groups']));
        $this->assertTrue(is_array($config['routes']['groups-permissions']));

        // users - padrão
        $route_params = ['base', 'index', 'create', 'store', 'edit', 'update', 'delete'];
        foreach ($route_params as $param) {
            $this->assertArrayHasKey($param, $config['routes']['users']);
            if ($param == 'base') {
                $this->assertEquals("laracl/users", $config['routes']['users']['base']);
            } else {
                $this->assertEquals("users.$param", $config['routes']['users'][$param]);
            }
        }

        // groups -> personalizado
        $route_params = ['base', 'index', 'create', 'store', 'edit', 'update', 'delete'];
        foreach ($route_params as $param) {
            $this->assertArrayHasKey($param, $config['routes']['groups']);
            if ($param == 'base') {
                $this->assertEquals("painel/groups", $config['routes']['groups']['base']);
            } else {
                $this->assertEquals("groups.$param", $config['routes']['groups'][$param]);
            }
        }

        // A configuração não é normalizada se já estiver ok
        $this->assertFalse(\Laracl\Core::normalizeConfig());

    }

    public function testGetUserPermissionsNull()
    {
        $user = self::createUser();
        $role = self::createRole();

        $this->assertNull(\Laracl\Core::getDebug('current_ability_origin'));

        $this->assertNull(session('user.abilities'));
        $permissions = \Laracl\Core::getUserPermissions($user->id, $role->slug);
        $this->assertNotNull(session('user.abilities'));

        // As permissões foram adquiridas do usuário
        $this->assertNull($permissions);
        $this->assertNull(\Laracl\Core::getDebug('current_ability_origin'));
    }

    public function testGetUserPermissionsFromUser()
    {
        $user = self::createUser();
        $role = self::createRole();
        $permissions = self::createUserPermissions($role->id, $user->id, true, false, true, true);

        $this->assertNull(\Laracl\Core::getDebug('current_ability_origin'));

        $this->assertNull(session('user.abilities'));
        $permissions = \Laracl\Core::getUserPermissions($user->id, $role->slug);
        $this->assertNotNull(session('user.abilities'));

        // As permissões foram adquiridas do usuário
        $this->assertNotNull($permissions);
        $this->assertTrue(is_array($permissions));
        $this->assertEquals('user', \Laracl\Core::getDebug('current_ability_origin'));
    }

    public function testGetUserPermissionsFromGroup()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);
        $role = self::createRole();
        $permissions = self::createGroupPermissions($role->id, $group->id, true, false, true, true);

        $this->assertNull(\Laracl\Core::getDebug('current_ability_origin'));

        $this->assertNull(session('user.abilities'));
        $permissions = \Laracl\Core::getUserPermissions($user->id, $role->slug);
        $this->assertNotNull(session('user.abilities'));

        // As permissões foram adquiridas do grupo
        $this->assertNotNull($permissions);
        $this->assertTrue(is_array($permissions));
        $this->assertEquals('group', \Laracl\Core::getDebug('current_ability_origin'));
    }

    public function testGetUserPermissionsFromUsePrecedence()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);
        $role = self::createRole();

        // Existem permissãoes de grupo e de usuário para este usuário
        self::createGroupPermissions($role->id, $group->id, true, false, true, true);
        self::createUserPermissions($role->id, $user->id, true, false, true, true);

        $this->assertNull(\Laracl\Core::getDebug('current_ability_origin'));

        $this->assertNull(session('user.abilities'));
        $permissions = \Laracl\Core::getUserPermissions($user->id, $role->slug);
        $this->assertNotNull(session('user.abilities'));

        // As permissões foram adquiridas do usuário por precedência
        $this->assertNotNull($permissions);
        $this->assertTrue(is_array($permissions));
        $this->assertEquals('user', \Laracl\Core::getDebug('current_ability_origin'));
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
            $this->assertTrue(\Laracl\Core::userCan($root_user, $role_one->slug, $permission));
            $this->assertEquals('config', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_one->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);

            // Função de acesso 2
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertTrue(\Laracl\Core::userCan($root_user, $role_two->slug, $permission));
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
            $this->assertTrue(\Laracl\Core::userCan($user->id, $role_one->slug, $permission));
            $this->assertEquals('user', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_one->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);

            // Função de acesso 2
            // Todas as permissões são false
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse(\Laracl\Core::userCan($user->id, $role_two->slug, $permission));
            $this->assertEquals('user', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_two->slug,
                'permission' => $permission,
                'granted'    => false,
            ]);

            // Função de acesso 3
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse(\Laracl\Core::userCan($user->id, $role_three->slug, $permission));
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
            $this->assertTrue(\Laracl\Core::userCan($user->id, $role_one->slug, $permission));
            $this->assertEquals('group', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_one->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);

            // Função de acesso 2
            // Todas as permissões são false
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse(\Laracl\Core::userCan($user->id, $role_two->slug, $permission));
            $this->assertEquals('group', \Laracl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Laracl\Core::getDebug('current_ability'), [
                'role'       => $role_two->slug,
                'permission' => $permission,
                'granted'    => false,
            ]);

            // Função de acesso 3
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse(\Laracl\Core::userCan($user->id, $role_three->slug, $permission));
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

            $this->assertTrue(\Laracl\Core::userCan($user->id, $role_one->slug, $permission, function(){ return true; }));
            $this->assertTrue(\Laracl\Core::userCan($user->id, $role_two->slug, $permission, function(){ return true; }));

            $this->assertFalse(\Laracl\Core::userCan($user->id, $role_one->slug, $permission, function(){ return false; }));
            $this->assertFalse(\Laracl\Core::userCan($user->id, $role_two->slug, $permission, function(){ return false; }));
        };
    }

    public function testRegisterPoliciesRolesException()
    {
        \Laracl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\OutOfRangeException::class);

        // é obrigatória a existencia de Roles na configuração
        config(['laracl.roles' => null ]);
        \Laracl\Core::registerPolicies();
    }

    public function testRegisterPoliciesNoPermissionsException()
    {
        \Laracl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\InvalidArgumentException::class);

        // É obrigatória a existencia do indice 'permissions' na configuração de Roles
        config(['laracl.roles.users' => null ]);
        config(['laracl.roles.users.label' => 'Usuários' ]);
        \Laracl\Core::registerPolicies();
    }

    public function testRegisterPoliciesInvalidPermissionsParamExceptionOne()
    {
        \Laracl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\InvalidArgumentException::class);

        // O indice 'permissions' na configuração de Roles deve ser uma string
        config(['laracl.roles.users' => null ]);
        config([
            'laracl.roles.users.label' => 'Usuários',
            'laracl.roles.users.permissions' => null
        ]);
        \Laracl\Core::registerPolicies();
    }

    public function testRegisterPoliciesInvalidPermissionsParamExceptionTwo()
    {
        \Laracl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\InvalidArgumentException::class);

        // O indice 'permissions' na configuração de Roles deve ser uma string
        config(['laracl.roles.users' => null ]);
        config([
            'laracl.roles.users.label' => 'Usuários',
            'laracl.roles.users.permissions' => 456
        ]);
        \Laracl\Core::registerPolicies();
    }

    public function testRegisterPoliciesInvalidPermissionsValueExceptionTwo()
    {
        \Laracl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\UnexpectedValueException::class);

        // Apenas create,read,update e delete são permissões válidas
        config(['laracl.roles.users' => null ]);
        config([
            'laracl.roles.users.label'       => 'Usuários',
            'laracl.roles.users.permissions' => 'create,invalid,update,delete'
        ]);
        \Laracl\Core::registerPolicies();
    }

    public function testRegisterPolicies()
    {
        \Laracl\Core::resetCore(); // limpa todos os lazy loads
        $system_roles = config('laracl.roles');

        \Laracl\Core::registerPolicies();

        $registered = \Laracl\Core::getDebug('registered_polices');

        $compared = [];
        foreach ($system_roles as $role => $item) {
            foreach (explode(',', $item['permissions']) as $permission) {
                $compared[] = [
                    'role'       => $role,
                    'permission' => $permission
                ];
            }
        }

        $this->assertEquals($registered, $compared);
    }
}
