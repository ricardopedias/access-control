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
