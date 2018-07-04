<?php
namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AclUser extends \App\User
{
    use SoftDeletes;

    protected $table = 'users';

    /**
     * Os atributos que devem ser mudados para datas
     *
     * @see https://laravel.com/docs/5.6/eloquent#soft-deleting
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Devolve os campos usados para atualização de dados.
     * Serão necessários para a criação dos CRUDs que gerenciam
     * as permissões dos usuários
     * @return array
     */
    public function getFillableColumns()
    {
        return $this->fillable;
    }

    /**
     * Devolve o modelo de relacionamento entre o usuário e o grupo
     * @return Laracl\Models\AclUserGroup ou null
     */
    public function groupRelation()
    {
        return $this->hasOne(AclUserGroup::class,'user_id', 'id');
    }

    /**
     * Devolve as funções deste usuário
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function roles()
    {
        return $this->belongsToMany(AclRole::class,
            'acl_users_permissions', // inner join
            'user_id', // acl_users_permissions.user_id = chave primária de AclUser
            'role_id'  // acl_users_permissions.role_id = chave primária de AclRole
        );
    }

    /**
     * Devolve as permissões deste usuário
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function permissions()
    {
        return $this->hasMany(AclUserPermission::class, 'user_id', 'id');
    }

    /**
     * Verifica se o usuário possui permissão na função especificada
     * @param  int $role_id    ID da função
     * @param  string $permission create, read, update ou delete
     * @return boolean
     */
    public function canCheck($role_id, $permission)
    {
        $model =  $this->permissions()->where('role_id', $role_id)->first();
        return $model==null
            ? false
            : ($model->$permission == 'yes');
    }

    /**
     * Verifica se o usuário pode criar na função especificada.
     * @param  int $role_id    ID da função
     * @return boolean
     */
    public function canCreate($role_id)
    {
        return $this->canCheck($role_id, 'create');
    }

    /**
     * Verifica se o usuário pode ler na função especificada.
     * @param  int $role_id    ID da função
     * @return boolean
     */
    public function canRead($role_id)
    {
        return $this->canCheck($role_id, 'read');
    }

    /**
     * Verifica se o usuário pode editar na função especificada.
     * @param  int $role_id    ID da função
     * @return boolean
     */
    public function canUpdate($role_id)
    {
        return $this->canCheck($role_id, 'update');
    }

    /**
     * Verifica se o usuário pode excluir na função especificada.
     * @param  int $role_id    ID da função
     * @return boolean
     */
    public function canDelete($role_id)
    {
        return $this->canCheck($role_id, 'delete');
    }
}
