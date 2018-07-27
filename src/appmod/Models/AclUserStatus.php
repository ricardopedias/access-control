<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Models;

use Illuminate\Database\Eloquent\Model;

class AclUserStatus extends Model
{
    protected $table = 'acl_users_status';

    protected $primaryKey = 'user_id';

    protected $attributes = [
        'user_id'      => null,
        'access_panel' => 'no',
        'status'       => 'active'
    ];

    /**
     * Os atributos que podem ser setados em massa
     *
     * @see https://laravel.com/docs/5.6/eloquent#mass-assignment
     * @var array
     */
    protected $fillable = [
        'user_id',
        'access_panel',
        'status',
    ];

    public $timestamps = false;

    /**
     * Devolve o modelo do usuário ao qual este relacionamento pertence.
     * @return Acl\Models\AclUser ou null
     */
    public function user()
    {
        return $this->hasOne(AclUser::class, 'id', 'user_id');
    }
}
