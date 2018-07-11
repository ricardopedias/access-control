<?php
namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;

class AclRole extends Model
{
    protected $primaryKey = 'id';

    /**
     * Os atributos que podem ser setados em massa
     *
     * @see https://laravel.com/docs/5.6/eloquent#mass-assignment
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Devolve a coleção de usuários que possuem esta função de acesso.
     * @return Illuminate\Database\Eloquent\Collection ou null
     */
    public function users()
    {
        return $this->belongsToMany(AclUser::class,
            'acl_users_permissions', // inner join
            'role_id', // acl_users_permissions.role_id = chave primária de AclRole
            'user_id'  // acl_users_permissions.user_id = chave primária de AclUser
        );
    }

    /**
     * Devolve a coleção de grupos que possuem esta função de acesso.
     * @return Illuminate\Database\Eloquent\Collection ou null
     */
    public function groups()
    {
        return $this->belongsToMany(AclGroup::class,
            'acl_groups_permissions', // inner join
            'role_id', // acl_groups_permissions.role_id = chave primária de AclRole
            'group_id'  // acl_groups_permissions.group_id = chave primária de AclGroup
        );
    }

    /**
     * Devolve o modelo com as permissões de usuário para esta função de acesso
     * @return Laracl\Models\AclUserPermission ou null
     */
    public function usersPermissions()
    {
        return $this->hasMany(AclUserPermission::class, 'role_id', 'id');
    }

    /**
     * Devolve o modelo com as permissões de grupo para esta função de acesso
     * @return Laracl\Models\AclGroupPermission ou null
     */
    public function groupsPermissions()
    {
        return $this->hasMany(AclGroupPermission::class, 'role_id', 'id');
    }
}
