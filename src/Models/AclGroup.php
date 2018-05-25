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

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getFillableColumns()
    {
        return $this->fillable;
    }

    /**
     * Devolve o modelo do grupo ao qual este usuário pertence.
     * @return Illuminate\Database\Eloquent\Collection ou null
     */
    public function users()
    {
        return $this->hasMany(AclUser::class, 'id', 'group_id');
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
