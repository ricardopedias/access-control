<?php
namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Services\RolesService;

class RolesServiceTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testGetStructure()
    {
        $abilities = config('acl.roles');

        $structure = (new RolesService)->getStructure();

        foreach($abilities as $role => $data) {

            $this->assertArrayHasKey($role, $structure);

            $label = $data['label'];
            $permissions = trim(str_replace(' ', '', $data['permissions']), ',');
            $crud_all = array_flip(['create', 'read', 'update', 'delete']);
            $crud_setted = explode(',', $data['permissions']);
            foreach($crud_setted as $perm) {
                $this->assertArrayHasKey('permissions', $structure[$role]);
                $this->assertArrayHasKey($perm, $structure[$role]['permissions']);
                $this->assertNotNull($structure[$role]['permissions'][$perm]);
                unset($crud_all[$perm]);
            }
            foreach($crud_all as $perm => $nulled) {
                $this->assertArrayHasKey('permissions', $structure[$role]);
                $this->assertArrayHasKey($perm, $structure[$role]['permissions']);
                $this->assertNull($structure[$role]['permissions'][$perm]);
            }
        }
    }
}
