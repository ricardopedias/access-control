<?php

namespace Laracl\Models;

use Illuminate\Database\Eloquent\Model;

class AclRole extends Model
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
        return $this->hasOne('Laracl\Models\AclPermission', 'id', 'role_id');
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
