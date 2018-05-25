<?php

namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AclGroup extends Model
{
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'system',
    ];

    /**
     * Quando um grupo for criado, a slug será
     * gerada automaticamente com base na slug do nome.
     * @param string $value
     */
    public function setNameAttribute(string $value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Devolve os campos usados para atualização de dados.
     * Serão necessários para a criação dos CRUDs que gerenciam
     * as permissões dos grupos
     * @return array
     */
    public function getFillableColumns() : array
    {
        return $this->fillable;
    }

    /**
     * Devolve o modelo do grupo ao qual este usuário pertence.
     * @return Illuminate\Database\Eloquent\Collection ou null
     */
    public function users()
    {
        return $this->belongsToMany(AclUser::class,
            'acl_users_groups', // inner join
            'group_id', // acl_users_groups.group_id = chave primária de AclGroup
            'user_id'  // acl_users_groups.user_id = chave primária de AclUser
        );
    }

    /**
     * Devolve as funções deste grupo
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function roles()
    {
        return $this->belongsToMany(AclRole::class,
            'acl_groups_permissions', // inner join
            'group_id', // acl_groups_permissions.group_id = chave primária de AclGroup
            'role_id'  // acl_groups_permissions.role_id = chave primária de AclRole
        );
    }

    /**
     * Devolve as permissões deste grupo
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function permissions()
    {
        return $this->hasMany(AclGroupPermission::class, 'group_id', 'id');
    }

    /**
     * Verifica se o grupo possui permissão na função especificada
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
     * Verifica se o grupo pode criar na função especificada.
     * @param  int $role_id    ID da função
     * @return boolean
     */
    public function canCreate($role_id)
    {
        return $this->canCheck($role_id, 'create');
    }

    /**
     * Verifica se o grupo pode ler na função especificada.
     * @param  int $role_id    ID da função
     * @return boolean
     */
    public function canRead($role_id)
    {
        return $this->canCheck($role_id, 'read');
    }

    /**
     * Verifica se o grupo pode editar na função especificada.
     * @param  int $role_id    ID da função
     * @return boolean
     */
    public function canUpdate($role_id)
    {
        return $this->canCheck($role_id, 'update');
    }

    /**
     * Verifica se o grupo pode excluir na função especificada.
     * @param  int $role_id    ID da função
     * @return boolean
     */
    public function canDelete($role_id)
    {
        return $this->canCheck($role_id, 'delete');
    }
}
