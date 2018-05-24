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
     * The attributes that are mass assignable.
     *
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

    //
    // Relacionamentos
    //

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function group()
    {
        return $this->hasOne('Laracl\Models\AclGroup', 'id', 'group_id');
    }

    public function permissions()
    {
        return $this->hasMany('Laracl\Models\AclUserPermissions', 'user_id', 'id');
    }
}
