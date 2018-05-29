<?php

namespace Laracl\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;
use Laracl\Tests\Libs\IModelTestCase;

class AcessorTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testeNormalizeConfig()
    {
        // Sobrescreve a configuração para ela voltar ao normal
        // pois \Laracl::normalizeConfig() foi chamado no ServiceProvider
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
        $this->assertTrue(\Laracl::normalizeConfig());

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
        $this->assertFalse(\Laracl::normalizeConfig());
    }

    public function testSetCurrentAbility()
    {
        $this->assertTrue(true);
    }

    public function testGetUserPermissions()
    {
        $this->assertTrue(true);
    }

    public function testRegisterPolicies()
    {
        $this->assertTrue(true);
    }

}
