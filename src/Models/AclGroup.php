<?php

namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;

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

    //
    // Relacionamentos
    //

    public function permissions()
    {
        return $this->hasOne('Laracl\Models\AclGroupPermission', 'id', 'group_id');
    }

    public function users()
    {
        return $this->hasMany('Laracl\Models\AclUsers', 'id', 'acl_group_id');
    }

    //
    // Métodos Especiais
    //

    /**
     * Devolve uma função a partir de sua slug
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $slug 
     * @return \Laracl\Models\AclRole
     */
    public static function findBySlug($slug)
    {
        return (new static)->where('slug', $slug)->first();
    }
}
