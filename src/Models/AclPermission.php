<?php

namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;
use Laracl\Traits\HasCompositePrimaryKey;

class AclPermission extends Model
{
    use HasCompositePrimaryKey;

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
        'create',
        'edit',
        'show',
        'delete',
    ];

    //
    // Relacionamentos
    //

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function role()
    {
        return $this->hasOne('Laracl\Models\AclRole', 'id', 'role_id');
    }

    //
    // Métodos Especiais
    //

    /**
     * Devolve as permissões do usuário na função especificada
     *
     * @param mixed $role A slug ou o ID da função
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function collectByUserRole($user_id, $role)
    {
        if (is_string($role) == true ) {

            $role = AclRole::findBySlug($role);
            if ($role == NULL) {
                return collect([]);
            }
            else {
                $role = $role->id;
            }
        }
        elseif(!is_numeric($role)) {
            throw new \InvalidArgumentException("The 'role' parameter must be an integer or a string");
        }

        // Usuário possui esta função ?
        // $user_role = AclUserRole::find([$user_id, $role]);
        // if ($user_role == NULL) {
        //     return collect([]);
        // }

        return (new static)->where('role_id', $role)->where('user_id', $user_id)->get();
    }

    /**
     * Devolve as permissões do usuário
     *
     * @param mixed $role A slug ou o ID da função
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function collectByUser($user_id)
    {
        return (new static)->where('user_id', $user_id)->get();
    }

    /**
     * Devolve as permissões disponiveis na função especificada
     *
     * @param mixed $role A slug ou o ID da função
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function collectByRole($role)
    {
        if (is_string($role) == true ) {

            $role = AclRole::findBySlug($role);
            if ($role == NULL) {
                return collect([]);
            }
            else {
                $role = $role->id;
            }
        }
        elseif(!is_numeric($role)) {
            throw new \InvalidArgumentException("The 'role' parameter must be an integer or a string");
        }

        return (new static)->where('role_id', $role)->get();
    }
}
