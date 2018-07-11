<?php
namespace Acl\Models;

use Illuminate\Database\Eloquent\Model;
use Acl\Traits\HasCompositePrimaryKey;

class AclUserPermission extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'acl_users_permissions';

    protected $primaryKey = ['role_id', 'user_id'];

    public $timestamps = false;

    /**
     * Os atributos que podem ser setados em massa
     *
     * @see https://laravel.com/docs/5.6/eloquent#mass-assignment
     * @var array
     */
    protected $fillable = [
        'role_id',
        'user_id',
        'create',
        'read',
        'update',
        'delete',
    ];

    /**
     * Devolve o modelo com a função de acesso para estas permissões
     *
     * @return Acl\Models\AclRole ou null
     */
    public function role()
    {
        return $this->hasOne(AclRole::class, 'id', 'role_id');
    }

    /**
     * Devolve o modelo do usuário ao qual perntencem estas permissões
     *
     * @return Acl\Models\AclUser ou null
     */
    public function user()
    {
        return $this->hasOne('Acl\Models\AclUser', 'id', 'user_id');
    }

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

    /**
     * Devolve as permissões do usuário
     *
     * @param integer $user_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function collectByUser($user_id)
    {
        return (new static)->where('user_id', $user_id)->get();
    }

    /**
     * Remove as permissões do usuário e
     * devolve o número de registros excluídos
     *
     * @param integer $user_id
     * @return integer
     */
    public static function removeByUser($user_id)
    {
        return (new static)->where('user_id', $user_id)->delete();
    }
}
