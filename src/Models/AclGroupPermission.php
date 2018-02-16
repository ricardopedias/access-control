<?php

namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;
use Laracl\Traits\HasCompositePrimaryKey;

class AclGroupPermission extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'acl_groups_permissions';

    protected $primaryKey = ['role_id', 'group_id'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id',
        'group_id',
        'create',
        'edit',
        'show',
        'delete',
    ];

    //
    // Relacionamentos
    //

    public function group()
    {
        return $this->hasOne('Laracl\Models\AclGroup', 'id', 'group_id');
    }

    public function role()
    {
        return $this->hasOne('Laracl\Models\AclRole', 'id', 'group_id');
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
    public static function collectByGroupRole($group_id, $role)
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

        return (new static)->where('role_id', $role)->where('group_id', $group_id)->get();
    }

    /**
     * Devolve as permissões do usuário
     *
     * @param mixed $role A slug ou o ID da função
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function collectByGroup($group_id)
    {
        return (new static)->where('group_id', $group_id)->get();
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
