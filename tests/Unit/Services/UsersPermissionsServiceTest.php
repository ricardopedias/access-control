<?php
namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Services\UsersPermissionsService;

class UsersPermissionsServiceTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testUpdate()
    {
        $user = self::createUser();

        //
        // Atualização sem permissões prévias
        // Dados novos serão inseridos!
        //
        $data = [
            'permissions' => [
                'users'              => ['exists' => '1', 'create' => 'yes'],
                'users-permissions'  => ['exists' => '1', 'create' => 'yes'],
                'groups'             => ['exists' => '1', 'create' => 'yes', 'read' => 'yes', 'update' => 'yes', 'delete' => 'yes'],
                'groups-permissions' => ['exists' => '1'],
            ]
        ];
        $updated = (new UsersPermissionsService)->dataUpdate($data, $user->id);

        $this->assertTrue($updated);

        // users
        $role_users = \Acl\Models\AclRole::where('slug', 'users')->first();
        $permissions_users = \Acl\Models\AclUserPermission::where('role_id', $role_users->id)
            ->where('user_id', $user->id)->first();
        $this->assertEquals('yes', $permissions_users->create);
        $this->assertEquals('no', $permissions_users->read);
        $this->assertEquals('no', $permissions_users->update);
        $this->assertEquals('no', $permissions_users->delete);

        // groups
        $role_groups = \Acl\Models\AclRole::where('slug', 'groups')->first();
        $permissions_groups = \Acl\Models\AclUserPermission::where('role_id', $role_groups->id)
            ->where('user_id', $user->id)->first();
        $this->assertEquals('yes', $permissions_groups->create);
        $this->assertEquals('yes', $permissions_groups->read);
        $this->assertEquals('yes', $permissions_groups->update);
        $this->assertEquals('yes', $permissions_groups->delete);

        // groups-permissions
        $role_groups_permissions = \Acl\Models\AclRole::where('slug', 'groups-permissions')->first();
        $permissions_groups_perms = \Acl\Models\AclUserPermission::where('role_id', $role_groups_permissions->id)
            ->where('user_id', $user->id)->first();
        $this->assertEquals('no', $permissions_groups_perms->create);
        $this->assertEquals('no', $permissions_groups_perms->read);
        $this->assertEquals('no', $permissions_groups_perms->update);
        $this->assertEquals('no', $permissions_groups_perms->delete);

        //
        // Atualização com permissões préviamente setadas
        // Os dados serão trocados pelos novos!
        //
        $data = [
            'permissions' => [
                'users'              => ['exists' => '1'],
                'users-permissions'  => ['exists' => '1'],
                'groups'             => ['exists' => '1', 'read' => 'yes'],
                'groups-permissions' => ['exists' => '1'],
            ]
        ];
        $updated = (new UsersPermissionsService)->dataUpdate($data, $user->id);

        $this->assertTrue($updated);

        // users
        $role_users = \Acl\Models\AclRole::where('slug', 'users')->first();
        $permissions_users = \Acl\Models\AclUserPermission::where('role_id', $role_users->id)
            ->where('user_id', $user->id)->first();
        $this->assertEquals('no', $permissions_users->create);
        $this->assertEquals('no', $permissions_users->read);
        $this->assertEquals('no', $permissions_users->update);
        $this->assertEquals('no', $permissions_users->delete);

        // groups
        $role_groups = \Acl\Models\AclRole::where('slug', 'groups')->first();
        $permissions_groups = \Acl\Models\AclUserPermission::where('role_id', $role_groups->id)
            ->where('user_id', $user->id)->first();
        $this->assertEquals('no', $permissions_groups->create);
        $this->assertEquals('yes', $permissions_groups->read);
        $this->assertEquals('no', $permissions_groups->update);
        $this->assertEquals('no', $permissions_groups->delete);
    }

    public function testGetStructure_Default()
    {
        $user = self::createUser();

        $abilities = config('acl.roles');

        $structure = (new UsersPermissionsService)->getStructure($user->id);

        foreach($abilities as $role => $data) {

            $this->assertArrayHasKey($role, $structure);

            $label = $data['label'];
            $this->assertArrayHasKey('label', $structure[$role]);
            $this->assertEquals($label, $structure[$role]['label']);

            $permissions = trim(str_replace(' ', '', $data['permissions']), ',');
            $crud_all = array_flip(['create', 'read', 'update', 'delete']);
            $crud_setted = explode(',', $data['permissions']);
            foreach($crud_setted as $perm) {
                $this->assertArrayHasKey('permissions', $structure[$role]);
                $this->assertArrayHasKey($perm, $structure[$role]['permissions']);
                $this->assertNotNull($structure[$role]['permissions'][$perm]);
                $this->assertEquals('no', $structure[$role]['permissions'][$perm]);
                unset($crud_all[$perm]);
            }
            foreach($crud_all as $perm => $nulled) {
                $this->assertArrayHasKey('permissions', $structure[$role]);
                $this->assertArrayNotHasKey($perm, $structure[$role]['permissions']);
            }
        }
    }

    public function testGetStructure_Null()
    {
        $user = self::createUser();
        $structure = (new UsersPermissionsService)->getStructure($user->id, true);
        $this->assertNull($structure);
    }

    public function testGetStructure_UserPermissions()
    {
        $role = \Acl\Models\AclRole::where('slug', 'users')->first();
        $user = self::createUser();
        self::createUserPermissions($role->id, $user->id, true, true, true, true);

        $abilities = config('acl.roles');

        $structure = (new UsersPermissionsService)->getStructure($user->id);

        foreach($abilities as $role => $data) {

            $label = $data['label'];
            $permissions = trim(str_replace(' ', '', $data['permissions']), ',');
            $crud_setted = explode(',', $data['permissions']);
            $crud_all = array_flip(['create', 'read', 'update', 'delete']);

            $this->assertArrayHasKey($role, $structure);
            $this->assertArrayHasKey('permissions', $structure[$role]);

            if ($role == 'users') {
                foreach($crud_setted as $perm) {
                    $this->assertArrayHasKey('permissions', $structure[$role]);
                    $this->assertArrayHasKey($perm, $structure[$role]['permissions']);
                    $this->assertNotNull($structure[$role]['permissions'][$perm]);
                    $this->assertEquals('yes', $structure[$role]['permissions'][$perm]);
                    unset($crud_all[$perm]);
                }
            } else {
                $crud_all = array_flip(['create', 'read', 'update', 'delete']);
                foreach($crud_setted as $perm) {
                    $this->assertArrayHasKey('permissions', $structure[$role]);
                    $this->assertArrayHasKey($perm, $structure[$role]['permissions']);
                    $this->assertNotNull($structure[$role]['permissions'][$perm]);
                    $this->assertEquals('no', $structure[$role]['permissions'][$perm]);
                    unset($crud_all[$perm]);
                }
            }

            // Os cruds restantes não forem setados!!
            foreach($crud_all as $perm => $nulled) {
                $this->assertArrayHasKey('permissions', $structure[$role]);
                $this->assertArrayNotHasKey($perm, $structure[$role]['permissions']);
            }
        }
    }

    public function testGetUserPermissionsNull()
    {
        $user = self::createUser();
        $role = self::createRole();

        $this->assertNull(\Acl\Core::getDebug('current_ability_origin'));

        $this->assertNull(session('user.abilities'));
        $permissions = (new UsersPermissionsService)->getPermissionsByUserID($user->id, $role->slug);
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
        $permissions = (new UsersPermissionsService)->getPermissionsByUserID($user->id, $role->slug);
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
        $permissions = (new UsersPermissionsService)->getPermissionsByUserID($user->id, $role->slug);
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
        $permissions = (new UsersPermissionsService)->getPermissionsByUserID($user->id, $role->slug);
        $this->assertNotNull(session('user.abilities'));

        // As permissões foram adquiridas do usuário por precedência
        $this->assertNotNull($permissions);
        $this->assertTrue(is_array($permissions));
        $this->assertEquals('user', \Acl\Core::getDebug('current_ability_origin'));
    }
}
