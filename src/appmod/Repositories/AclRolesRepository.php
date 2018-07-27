<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Repositories;

use Acl\Models\AclRole;

class AclRolesRepository extends BaseRepository
{
    protected $model_class = AclRole::class;

    public function findBySlug($slug, bool $failable = false)
    {
        $info = config("acl.roles.{$slug}");

        if (!isset($info) || !isset($info['label'])) {
            if($failable == true) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
            } else {
                return null;
            }
        }

        $role = $this->newQuery()->where('slug', $slug)->first();

        // Se a função de acesso nunca foi invocada, deve ser criada
        if ($role == null) {

            $this->create([
                'name'        => $info['label'],
                'slug'        => $slug,
                'description' => $info['description'] ?? ''
            ]);
        }

        $builder = $this->newQuery()->where('slug', $slug);
        if($failable == true) {
            return $builder->firstOrFail();
        } else {
            return $builder->first();
        }
    }
}
