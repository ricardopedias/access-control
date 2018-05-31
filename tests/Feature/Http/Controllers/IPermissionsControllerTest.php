<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IControllerTestCase;
use Laracl\Tests\Libs\IPermissionsControllerAccessor;

class IPermissionsControllerTest extends IControllerTestCase
{
    use RefreshDatabase;

    // Para fverifica as rotas disponiveis
    // php artisan route:list

    public function testGetRolesStructure()
    {
        $controller = new IPermissionsControllerAccessor;

        // As abilities são registradas pelo método Core::registerPolicies
        // que é invocado em ServiceProvide::boot

        $roles = $controller->accessGetRolesStructure();

        $abilities = \Gate::abilities();

        foreach ($roles as $role => $data) {

            foreach ($data['roles'] as $perm => $value) {
                if($value === null) {
                    $this->assertArrayNotHasKey($role . "." . $perm, $abilities);
                } elseif($value == "") {
                    $this->assertArrayHasKey($role . "." . $perm, $abilities);
                }
            }
        }

        // TODO
        // Quando o teste é efetuado invocando somente este arquivo,
        // \Gate::abilities() devolve a lista de habilidades registradas no ServiceProvider
        // Quando todos os testes são executados de uma vez,
        // \Gate::abilities() devolve um array vazio
        $this->assertTrue(true);
    }

    public function testPopulateStructure()
    {
        $this->assertTrue(true);
    }

    public function testGetSyncedRole()
    {
        $this->assertTrue(true);
    }
}
