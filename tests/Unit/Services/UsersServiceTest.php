<?php
namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Repositories\AclUsersRepository;
use Laracl\Repositories\AclGroupsRepository;
use Laracl\Services\UsersService;

class UsersServiceTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testGetSearcheable()
    {
        $group_one = self::createGroup();
        $group_two = self::createGroup();
        $inserteds = [];
        for ($x=1; $x<=10; $x++) {
            $rand_values = [$group_one->id, $group_two->id, null];
            $group_id    = $rand_values[mt_rand(0, count($rand_values) - 1)];
            $user_model  = self::createUser($group_id);
            $inserteds[$user_model->id] = $user_model;
        }

        $collection = (new UsersService)->getSearcheable()->get();
        // Usuário inicial + 10 adicionados neste teste
        $this->assertCount(1 + 10, $collection);

        foreach ($collection as $item) {
            $this->assertInstanceOf(\Laracl\Models\AclUser::class, $item);
        }
    }

    public function testCreateFull()
    {
        $group = self::createGroup();

        $data = [
            'name'     => self::faker()->name,
            'email'    => self::faker()->unique()->safeEmail,
            'password' => 'secret',
            'group_id' => $group->id
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password);
        $this->assertTrue(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertEquals($group->id, $group_saved->id);
        $this->assertEquals($group->name, $group_saved->name);
        $this->assertEquals($group->description, $group_saved->description);
        $this->assertEquals($group->system, $group_saved->system);
        $this->assertEquals($group->updatet_at, $group_saved->updatet_at);
        $this->assertEquals($group->created_at, $group_saved->created_at);
    }

    public function testCreate_WithoutGroup()
    {
        $data = [
            'name'     => self::faker()->name,
            'email'    => self::faker()->unique()->safeEmail,
            'password' => 'secret',
            //'group_id' => $group->id
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password);
        $this->assertTrue(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertNull($group_saved);
    }

    public function testCreateFull_GroupNull()
    {
        $data = [
            'name'     => self::faker()->name,
            'email'    => self::faker()->unique()->safeEmail,
            'password' => 'secret',
            'group_id' => null
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password);
        $this->assertTrue(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertNull($group_saved);
    }

    public function testCreateFull_WithoutPassword()
    {
        $group = self::createGroup();

        $data = [
            'name'       => self::faker()->name,
            'email'      => self::faker()->unique()->safeEmail,
            //'password' => 'secret',
            'group_id'   => $group->id
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password); // um password qualquer foi usado
        $this->assertFalse(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertEquals($group->id, $group_saved->id);
        $this->assertEquals($group->name, $group_saved->name);
        $this->assertEquals($group->description, $group_saved->description);
        $this->assertEquals($group->system, $group_saved->system);
        $this->assertEquals($group->updatet_at, $group_saved->updatet_at);
        $this->assertEquals($group->created_at, $group_saved->created_at);
    }

    public function testCreateFull_PasswordNull()
    {
        $group = self::createGroup();

        $data = [
            'name'     => self::faker()->name,
            'email'    => self::faker()->unique()->safeEmail,
            'password' => null,
            'group_id' => $group->id
        ];
        $model = (new UsersService)->dataInsert($data);

        $saved = (new AclUsersRepository)->read($model->id);
        $this->assertEquals($data['name'], $saved->name);
        $this->assertEquals($data['email'], $saved->email);
        $this->assertNotNull($saved->password); // um password qualquer foi usado
        $this->assertFalse(Hash::check('secret', $saved->password));

        $group_saved = (new AclGroupsRepository)->findByUserID($model->id);
        $this->assertEquals($group->id, $group_saved->id);
        $this->assertEquals($group->name, $group_saved->name);
        $this->assertEquals($group->description, $group_saved->description);
        $this->assertEquals($group->system, $group_saved->system);
        $this->assertEquals($group->updatet_at, $group_saved->updatet_at);
        $this->assertEquals($group->created_at, $group_saved->created_at);
    }
}
