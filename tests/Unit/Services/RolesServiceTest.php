<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

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
                // Devem existir as quatro habilidades, mesmo que
                // não estejam setadas nas habilidades
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
