<?php

namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;

class AclUser extends \App\User
{
    protected $table = 'users';

    public function getFillableColumns()
    {
        return $this->fillable;
    }

    //
    // Relacionamentos
    //

    public function groupRelation()
    {
        return $this->hasOne('Laracl\Models\AclUserGroup','user_id', 'id');
    }

    public function group()
    {
        $relation = $this->hasOne('Laracl\Models\AclUserGroup','user_id', 'id');
        return isset($relation->group) ? $relation->group : $relation;
    }

    public function permissions()
    {
        return $this->hasMany('Laracl\Models\AclUserPermissions', 'user_id', 'id');
    }
}
