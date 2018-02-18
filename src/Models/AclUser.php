<?php

namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;

class AclUser extends \App\User
{
    protected $table = 'users';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        /**
         * The attributes that are mass assignable.
         */
        $this->fillable[] = 'acl_group_id';
    }

    //
    // Relacionamentos
    //

    public function group()
    {
        return $this->hasOne('Laracl\Models\AclGroup', 'id', 'acl_group_id');
    }

    public function permissions()
    {
        return $this->hasMany('Laracl\Models\AclUserPermissions', 'user_id', 'id');
    }
}
