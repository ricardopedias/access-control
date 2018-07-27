<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Repositories\AclUsersRepository;
use Acl\Repositories\AclGroupsRepository;
use Acl\Services\GroupsService;

class GroupsServiceTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testGetSearcheable()
    {
        // Dois grupos existem por padrão
        // Admin e Usuários
        $table = (new \Acl\Models\AclGroup)->getTable();
        $default = \DB::table($table)->count();

        for ($x=1; $x<=10; $x++) {
            $user_model  = self::createGroup();
        }

        $collection = (new GroupsService)->getSearcheable()->get();
        // Grupos iniciais + 10 adicionados neste teste
        $this->assertCount($default + 10, $collection);

        foreach ($collection as $item) {
            $this->assertInstanceOf(\Acl\Models\AclGroup::class, $item);
        }
    }

    // Criação de grupos

    public function testCreate()
    {
        $data = [
            'name'        => ($name = self::faker()->name),
            'slug'        => Str::slug($name),
            'description' => self::faker()->paragraph(1),
            'system'      => 'no'
        ];
        $model = (new GroupsService)->dataInsert($data);
        $this->assertInstanceOf(\Acl\Models\AclGroup::class, $model);

        $saved = (new AclGroupsRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['slug'], $saved->slug);
        $this->assertEquals($data['description'], $saved->description);
        $this->assertEquals($data['system'], $saved->system);
    }

    // Atualização de Grupos

    public function testEditFull()
    {
        $group = self::createGroup();

        $saved = (new AclGroupsRepository)->read($group->id);
        $this->assertEquals($group->name, $saved->name);
        $this->assertEquals($group->slug, $saved->slug);
        $this->assertEquals($group->description, $saved->description);
        $this->assertEquals($group->system, $saved->system);

        $data = [
            'name'        => ($name = self::faker()->name),
            'slug'        => Str::slug($name),
            'description' => self::faker()->paragraph(1),
            'system'      => 'no'
        ];
        $update = (new GroupsService)->dataUpdate($group->id, $data);
        $this->assertTrue($update);

        $updated = (new AclGroupsRepository)->read($group->id);
        $this->assertNotEquals($group->name, $updated->name);
        $this->assertNotEquals($group->slug, $updated->slug);
        $this->assertNotEquals($group->description, $updated->description);
        $this->assertEquals($group->system, $updated->system); // no ou yes
    }
}
