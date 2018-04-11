<?php

namespace Laracl\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @see https://github.com/sebastianbergmann/phpunit-documentation-brazilian-portuguese/blob/master/src/assertions.rst
 */
class HelpersTest extends TestCase
{
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
        $this->assertCount(4, $config['roles']);
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
}
