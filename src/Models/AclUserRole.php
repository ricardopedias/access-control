<?php

namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;
use Laracl\Traits\HasCompositePrimaryKey;

class AclUserRole extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'acl_users_roles';

    protected $primaryKey = ['user_id', 'role_id'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'role_id',
    ];

    //
    // Relacionamentos
    //

    public function users()
    {
        return $this->hasMany('App\Users', 'id', 'user_id');
    }

    public function roles()
    {
        return $this->hasMany('Laracl\Models\AclRole', 'id', 'role_id');
    }

    //
    // Métodos Especiais
    //

    /**
     * Devolve as funções do usuário especificado.
     *
     * @param integer $user_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function collectByUser($user_id)
    {
        return (new static)->where('user_id', $user_id)->get();
    }

    /**
     * Devolve os usuários das funções especificadas
     *
     * @param integer $role_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function collectByRole($role_id)
    {
        return (new static)->where('role_id', $role_id)->get();
    }

    
}
