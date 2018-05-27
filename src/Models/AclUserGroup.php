<?php

namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;
use Laracl\Traits\HasCompositePrimaryKey;

class AclUserGroup extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'acl_users_groups';

    protected $primaryKey = ['user_id', 'group_id'];

    /**
     * Os atributos que podem ser setados em massa
     *
     * @see https://laravel.com/docs/5.6/eloquent#mass-assignment
     * @var array
     */
    protected $fillable = [
        'user_id',
        'group_id',
    ];

    public $timestamps = false;

    public function getFillableColumns()
    {
        return $this->fillable;
    }

    /**
     * Devolve o modelo do usuário ao qual este relacionamento pertence.
     * @return Laracl\Models\AclUser ou null
     */
    public function user()
    {
        return $this->hasOne(AclUser::class, 'id', 'user_id');
    }

    /**
     * Devolve o modelo do grupo ao qual este relacionamento pertence.
     * @return Laracl\Models\AclGroup ou null
     */
    public function group()
    {
        return $this->hasOne(AclGroup::class, 'id', 'group_id');
    }
}
