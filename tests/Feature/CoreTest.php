<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;
use Acl\Tests\Libs\IModelTestCase;
use Illuminate\Database\Eloquent\Collection;

class CoreTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testeDebug()
    {
        \Acl\Core::setDebug('test_param', 'test_value');

        // Primeira chamada devolve e exclui
        $debug = \Acl\Core::getDebug('test_param');
        $this->assertEquals($debug, 'test_value');
        // Debug excluído
        $this->assertNull(\Acl\Core::getDebug('test_param'));
    }

    public function testeNormalizeConfig()
    {
        // Sobrescreve a configuração para ela voltar ao normal
        // pois \Acl\Core::normalizeConfig() foi chamado no ServiceProvider
        config([
            'acl.routes.users'              => 'acl/users',
            'acl.routes.users-permissions'  => 'acl/users-permissions',
            'acl.routes.groups'             => 'painel/groups', // rota personalizada
            'acl.routes.groups-permissions' => 'acl/groups-permissions',
        ]);

        $config = config('acl');
        $this->assertArrayHasKey('users', $config['routes']);
        $this->assertArrayHasKey('users-permissions', $config['routes']);
        $this->assertArrayHasKey('groups', $config['routes']);
        $this->assertArrayHasKey('groups-permissions', $config['routes']);
        $this->assertTrue(is_string($config['routes']['users']));
        $this->assertTrue(is_string($config['routes']['users-permissions']));
        $this->assertTrue(is_string($config['routes']['groups']));
        $this->assertTrue(is_string($config['routes']['groups-permissions']));

        // Realiza o processo de normalização
        $this->assertTrue(\Acl\Core::normalizeConfig());

        $config = config('acl');
        $this->assertArrayHasKey('users', $config['routes']);
        $this->assertArrayHasKey('users-permissions', $config['routes']);
        $this->assertArrayHasKey('groups', $config['routes']);
        $this->assertArrayHasKey('groups-permissions', $config['routes']);
        $this->assertTrue(is_array($config['routes']['users']));
        $this->assertTrue(is_array($config['routes']['users-permissions']));
        $this->assertTrue(is_array($config['routes']['groups']));
        $this->assertTrue(is_array($config['routes']['groups-permissions']));

        // users - padrão
        $route_params = ['base', 'index', 'create', 'store', 'edit', 'update', 'destroy'];
        foreach ($route_params as $param) {
            $this->assertArrayHasKey($param, $config['routes']['users']);
            if ($param == 'base') {
                $this->assertEquals("acl/users", $config['routes']['users']['base']);
            } else {
                $this->assertEquals("users.$param", $config['routes']['users'][$param]);
            }
        }

        // groups -> personalizado
        $route_params = ['base', 'index', 'create', 'store', 'edit', 'update', 'destroy'];
        foreach ($route_params as $param) {
            $this->assertArrayHasKey($param, $config['routes']['groups']);
            if ($param == 'base') {
                $this->assertEquals("painel/groups", $config['routes']['groups']['base']);
            } else {
                $this->assertEquals("groups.$param", $config['routes']['groups'][$param]);
            }
        }

        // A configuração não é normalizada se já estiver ok
        $this->assertFalse(\Acl\Core::normalizeConfig());

    }

    public function testGetUserPermissionsNull()
    {
        $user = self::createUser();
        $role = self::createRole();

        $this->assertNull(\Acl\Core::getDebug('current_ability_origin'));

        $this->assertNull(session('user.abilities'));
        $permissions = \Acl\Core::getUserPermissions($user->id, $role->slug);
        $this->assertNotNull(session('user.abilities'));

        // As permissões foram adquiridas do usuário
        $this->assertNull($permissions);
        $this->assertNull(\Acl\Core::getDebug('current_ability_origin'));
    }

    public function testGetUserPermissionsFromUser()
    {
        $user = self::createUser();
        $role = self::createRole();
        $permissions = self::createUserPermissions($role->id, $user->id, true, false, true, true);

        $this->assertNull(\Acl\Core::getDebug('current_ability_origin'));

        $this->assertNull(session('user.abilities'));
        $permissions = \Acl\Core::getUserPermissions($user->id, $role->slug);
        $this->assertNotNull(session('user.abilities'));

        // As permissões foram adquiridas do usuário
        $this->assertNotNull($permissions);
        $this->assertTrue(is_array($permissions));
        $this->assertEquals('user', \Acl\Core::getDebug('current_ability_origin'));
    }

    public function testGetUserPermissionsFromGroup()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);
        $role = self::createRole();
        $permissions = self::createGroupPermissions($role->id, $group->id, true, false, true, true);

        $this->assertNull(\Acl\Core::getDebug('current_ability_origin'));

        $this->assertNull(session('user.abilities'));
        $permissions = \Acl\Core::getUserPermissions($user->id, $role->slug);
        $this->assertNotNull(session('user.abilities'));

        // As permissões foram adquiridas do grupo
        $this->assertNotNull($permissions);
        $this->assertTrue(is_array($permissions));
        $this->assertEquals('group', \Acl\Core::getDebug('current_ability_origin'));
    }

    public function testGetUserPermissionsFromUsePrecedence()
    {
        $group = self::createGroup();
        $user = self::createUser($group->id);
        $role = self::createRole();

        // Existem permissãoes de grupo e de usuário para este usuário
        self::createGroupPermissions($role->id, $group->id, true, false, true, true);
        self::createUserPermissions($role->id, $user->id, true, false, true, true);

        $this->assertNull(\Acl\Core::getDebug('current_ability_origin'));

        $this->assertNull(session('user.abilities'));
        $permissions = \Acl\Core::getUserPermissions($user->id, $role->slug);
        $this->assertNotNull(session('user.abilities'));

        // As permissões foram adquiridas do usuário por precedência
        $this->assertNotNull($permissions);
        $this->assertTrue(is_array($permissions));
        $this->assertEquals('user', \Acl\Core::getDebug('current_ability_origin'));
    }

    public function testRootUserCan()
    {
        $root_user = config('acl.root_user');
        $this->assertEquals($root_user, 1);

        $role_one = self::createRole();
        self::createUserPermissions($role_one->id, $root_user, true, true, true, true);

        $role_two = self::createRole();
        self::createUserPermissions($role_two->id, $root_user, false, false, false, false);

        foreach (['create', 'read', 'update', 'delete'] as $permission) {

            // Usuário root sempre tem acesso true

            // Função de acesso 1
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertTrue(\Acl\Core::userCan($root_user, $role_one->slug, $permission));
            $this->assertEquals('config', \Acl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Acl\Core::getDebug('current_ability'), [
                'role'       => $role_one->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);

            // Função de acesso 2
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertTrue(\Acl\Core::userCan($root_user, $role_two->slug, $permission));
            $this->assertEquals('config', \Acl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Acl\Core::getDebug('current_ability'), [
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
            $this->assertTrue(\Acl\Core::userCan($user->id, $role_one->slug, $permission));
            $this->assertEquals('user', \Acl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Acl\Core::getDebug('current_ability'), [
                'role'       => $role_one->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);

            // Função de acesso 2
            // Todas as permissões são false
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse(\Acl\Core::userCan($user->id, $role_two->slug, $permission));
            $this->assertEquals('user', \Acl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Acl\Core::getDebug('current_ability'), [
                'role'       => $role_two->slug,
                'permission' => $permission,
                'granted'    => false,
            ]);

            // Função de acesso 3
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse(\Acl\Core::userCan($user->id, $role_three->slug, $permission));
            $this->assertNull(\Acl\Core::getDebug('current_ability_origin'));
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
            $this->assertTrue(\Acl\Core::userCan($user->id, $role_one->slug, $permission));
            $this->assertEquals('group', \Acl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Acl\Core::getDebug('current_ability'), [
                'role'       => $role_one->slug,
                'permission' => $permission,
                'granted'    => true,
            ]);

            // Função de acesso 2
            // Todas as permissões são false
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse(\Acl\Core::userCan($user->id, $role_two->slug, $permission));
            $this->assertEquals('group', \Acl\Core::getDebug('current_ability_origin'));
            $this->assertEquals(\Acl\Core::getDebug('current_ability'), [
                'role'       => $role_two->slug,
                'permission' => $permission,
                'granted'    => false,
            ]);

            // Função de acesso 3
            session()->forget('user.abilities'); // exclui a sessão para evitar cache
            $this->assertFalse(\Acl\Core::userCan($user->id, $role_three->slug, $permission));
            $this->assertNull(\Acl\Core::getDebug('current_ability_origin'));
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

            $this->assertTrue(\Acl\Core::userCan($user->id, $role_one->slug, $permission, function(){ return true; }));
            $this->assertTrue(\Acl\Core::userCan($user->id, $role_two->slug, $permission, function(){ return true; }));

            $this->assertFalse(\Acl\Core::userCan($user->id, $role_one->slug, $permission, function(){ return false; }));
            $this->assertFalse(\Acl\Core::userCan($user->id, $role_two->slug, $permission, function(){ return false; }));
        };
    }

    public function testRegisterPoliciesRolesException()
    {
        \Acl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\OutOfRangeException::class);

        // é obrigatória a existencia de Roles na configuração
        config(['acl.roles' => null ]);
        \Acl\Core::registerPolicies();
    }

    public function testRegisterPoliciesNoPermissionsException()
    {
        \Acl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\InvalidArgumentException::class);

        // É obrigatória a existencia do indice 'permissions' na configuração de Roles
        config(['acl.roles.users' => null ]);
        config(['acl.roles.users.label' => 'Usuários' ]);
        \Acl\Core::registerPolicies();
    }

    public function testRegisterPoliciesInvalidPermissionsParamExceptionOne()
    {
        \Acl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\InvalidArgumentException::class);

        // O indice 'permissions' na configuração de Roles deve ser uma string
        config(['acl.roles.users' => null ]);
        config([
            'acl.roles.users.label' => 'Usuários',
            'acl.roles.users.permissions' => null
        ]);
        \Acl\Core::registerPolicies();
    }

    public function testRegisterPoliciesInvalidPermissionsParamExceptionTwo()
    {
        \Acl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\InvalidArgumentException::class);

        // O indice 'permissions' na configuração de Roles deve ser uma string
        config(['acl.roles.users' => null ]);
        config([
            'acl.roles.users.label' => 'Usuários',
            'acl.roles.users.permissions' => 456
        ]);
        \Acl\Core::registerPolicies();
    }

    public function testRegisterPoliciesInvalidPermissionsValueExceptionTwo()
    {
        \Acl\Core::resetCore(); // limpa todos os lazy loads
        $this->expectException(\UnexpectedValueException::class);

        // Apenas create,read,update e delete são permissões válidas
        config(['acl.roles.users' => null ]);
        config([
            'acl.roles.users.label'       => 'Usuários',
            'acl.roles.users.permissions' => 'create,invalid,update,delete'
        ]);
        \Acl\Core::registerPolicies();
    }

    public function testRegisterPolicies()
    {
        \Acl\Core::resetCore(); // limpa todos os lazy loads
        $system_roles = config('acl.roles');

        \Acl\Core::registerPolicies();

        $registered = \Acl\Core::getDebug('registered_polices');

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
