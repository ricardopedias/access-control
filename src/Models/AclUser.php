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

    /**
     * Devolve o modelo de relacionamento entre o usuário e o grupo
     * @return Laracl\Models\AclUserGroup ou null
     */
    public function groupRelation()
    {
        return $this->hasOne(AclUserGroup::class,'user_id', 'id');
    }

    /**
     * Devolve o modelo do grupo ao qual este usuário pertence.
     * @return Laracl\Models\AclGroup ou null
     */
    public function group()
    {
        $relation = $this->hasOne(AclUserGroup::class,'user_id', 'id');
        return isset($relation->group)
            ? $relation->group // chama método mágico de AclUserGroup
            : $relation; // null
    }

    public function roles()
    {
        //return $this->belongsToMany(AclRole::class, 'acl_users_permissions', 'id', 'role_id');
        return $this->belongsToMany(AclRole::class, 'acl_users_permissions', 'user_id', 'role_id');
        // ->withPivot('id', 'role_id');
        //return $this->hasMany(AclUserPermission::class, 'user_id', 'id');
        // ->wherePivot('approved', 1);
    }

    public function permissions()
    {
        return $this->hasMany(AclUserPermission::class, 'user_id', 'id');
    }
}
