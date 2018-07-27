<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Tests\Unit\Http\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Acl\Tests\Libs\IModelTestCase;
use Acl\Repositories\AclRolesRepository;

class RolesRepositoryTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testFindBySlug()
    {
        // Preparação
        config([
            'acl.roles.posts.label'       => 'Postagens',
            'acl.roles.posts.permissions' => 'create,read,update,delete',
        ]);

        $this->assertNull(\Acl\Models\AclRole::where('slug', 'posts')->first());

        // Verificações
        $model = (new AclRolesRepository)->findBySlug('posts');

        $this->assertInstanceOf(\Acl\Models\AclRole::class, $model);
        $this->assertNotNull(\Acl\Models\AclRole::where('slug', 'posts')->first());
        $model_compare = \Acl\Models\AclRole::where('slug', 'posts')->first();
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
