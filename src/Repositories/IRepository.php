<?php
namespace Laracl\Repositories;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\AbstractPaginator as Paginator;

/**
 * @see https://medium.com/by-vinicius-reis/repository-pattern-n%C3%A3o-precisa-ser-chato-principalmente-com-laravel-d97235b31c7e
 */
abstract class IRepository
{
    /**
     * Modelo usado no repositório
     *
     * @var string
     */
    protected $model_class;

    /**
     * Devolve uma instância do QueryBuilder
     *
     * @return EloquentQueryBuilder|QueryBuilder
     */
    protected function newQuery()
    {
        return app($this->model_class)->newQuery();
    }

    /**
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param int                               $take
     * @param bool                              $paginate
     *
     * @return EloquentCollection|Paginator
     */
    protected function doQuery($query = null, $take = 15, $paginate = true)
    {
        if (is_null($query)) {
            $query = $this->newQuery();
        }

        if (true == $paginate) {
            return $query->paginate($take);
        }

        if ($take > 0 || false !== $take) {
            $query->take($take);
        }

        return $query->get();
    }

    /**
     * Devolve todos os registros.
     * Se $take for false então devolve todos os registros
     * Se $paginate for true retorna uma instânca do Paginator
     *
     * @param int  $take
     * @param bool $paginate
     *
     * @return EloquentCollection|Paginator
     */
    public function getAll($take = 15, $paginate = true)
    {
        return $this->doQuery(null, $take, $paginate);
    }

    /**
    * @param string      $column
    * @param string|null $key
    *
    * @return \Illuminate\Support\Collection
    */
    public function lists($column, $key = null)
    {
        return $this->newQuery()->lists($column, $key);
    }

    /**
    * Devolve um registro com base em seu ID
    * Se $fail for true, falhas vão disparar ModelNotFoundException.
    *
    * @param int  $id
    * @param bool $fail
    *
    * @return Model
    */
    public function findByID($id, $fail = true)
    {
        if ($fail == true) {
            return $this->newQuery()->findOrFail($id);
        }

        return $this->newQuery()->find($id);
    }
}
