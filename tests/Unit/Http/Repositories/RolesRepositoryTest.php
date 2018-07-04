<?php
namespace Tests\Unit\Http\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laracl\Tests\Libs\IModelTestCase;
use Laracl\Repositories\AclRolesRepository;

class RolesRepositoryTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testFindBySlug()
    {
        // Preparação
        config([
            'laracl.roles.posts.label'       => 'Postagens',
            'laracl.roles.posts.permissions' => 'create,read,update,delete',
        ]);

        $this->assertNull(\Laracl\Models\AclRole::where('slug', 'posts')->first());

        // Verificações
        $model = (new AclRolesRepository)->findBySlug('posts');

        $this->assertInstanceOf(\Laracl\Models\AclRole::class, $model);
        $this->assertNotNull(\Laracl\Models\AclRole::where('slug', 'posts')->first());
        $model_compare = \Laracl\Models\AclRole::where('slug', 'posts')->first();
        $this->assertEquals($model, $model_compare);

        $this->assertEquals('posts', $model->slug, 'posts');
        $this->assertEquals('Postagens', $model->name);
        $this->assertEquals('', $model->description);
    }

    public function testFindBySlug_NotFound()
    {
        $model = (new AclRolesRepository)->findBySlug('xxx');
        $this->assertNull($model);
    }
}
