<?php
namespace Laracl\Repositories;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\AbstractPaginator as Paginator;

/**
 * @see https://medium.com/by-vinicius-reis/repository-pattern-n%C3%A3o-precisa-ser-chato-principalmente-com-laravel-d97235b31c7e
 */
abstract class BaseRepository
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
    public function newQuery()
    {
        return app($this->model_class)->newQuery();
    }

    /**
    * Devolve um novo modelo
    *
    * @return Model
    */
    public function newModel()
    {
        return $this->newQuery()->newModelInstance();
    }

    /**
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param int                               $take
     * @param bool                              $paginate
     *
     * @return EloquentCollection|Paginator
     */
    protected function doQuery($query = null, $take = 15, bool $paginate = true)
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
    * Se $failable for true, falhas vão disparar ModelNotFoundException.
    *
    * @param int  $id
    * @param bool $failable
    *
    * @return Model
    */
    public function findByID(int $id, bool $failable = true)
    {
        if ($failable == true) {
            return $this->newQuery()->findOrFail($id);
        }

        return $this->newQuery()->find($id);
    }

    /**
    * Devolve um registro com base no campo e valor especificados.
    * Se $failable for true, falhas vão disparar ModelNotFoundException.
    *
    * @param  mixed  $field
    * @param  mixed  $value
    * @param  bool $failable
    *
    * @return Model
    */
    public function findBy($field, $value = false, bool $failable = true)
    {
        $query = $this->newQuery();

        if(is_array($field)) {
            $failable = $value === true ? true : false;
            foreach ($field as $k => $v) {
                $query->where($k, $v);
            }
        } else {
            $query->where($field, $value);
        }

        if ($failable == true) {
            return $query->firstOrFail();
        }

        return $query->first();
    }

    /**
     * Devolve todos os registros.
     * Se $take for '0' então devolve todos os registros
     * Se $paginate for true retorna uma instânca do Paginator
     *
     * @param int  $take
     * @param bool $paginate
     *
     * @return EloquentCollection|Paginator
     */
    public function collectAll(int $take = 0, bool $paginate = true)
    {
        $take = $take > 0 ? $take : false;
        return $this->doQuery(null, $take, $paginate);
    }

    /**
    * Devolve uma lista de registros com base no campo e valor especificados.
    * Se $failable for true, falhas vão disparar ModelNotFoundException.
    *
    * @param  mixed  $field Nome do campo ou array com nomes e valores
    * @param  mixed  $value Valor do campo
    * @param int  $take
    * @param bool $paginate
    *
    * @return EloquentCollection|Paginator
    */
    public function collectBy($field, $value = null, int $take = 0, bool $paginate = true)
    {
        $query = $this->newQuery();

        if(is_array($field)) {
            foreach ($field as $k => $v) {
                $query->where($k, $v);
            }
        } else {
            $query->where($field, $value);
        }

        $take = $take > 0 ? $take : false;
        return $this->doQuery($query, $take, $paginate);
    }

    /**
     * Cria um novo registro
     *
     * @param  array  $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        return $this->newQuery()->create($data);
    }

    /**
     * Devolve os dados do registro especificado.
     * Se $id estiver presente, devolve os dados do registro
     * especificado, se ausente, devolve um modelo novo
     *
     * @param  int  $id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function read($id = null)
    {
        return ($id != null)
            ? $this->findByID($id)
            : $this->newModel();
    }

    /**
     * Atualiza os dados de um usuário existente.
     *
     * @param  int    $id
     * @param  array  $data
     * @return bool
     */
    public function update($id, array $data)
    {
        $model = $this->read($id)->fill($data);
        return $model->save();
    }

    /**
     * Remove o registro especificado do banco de dados.
     * Se $force for true, força a remoão o registro do banco
     * Isso é putil apenas para modelos com softdelete
     * @see https://laravel.com/docs/5.6/eloquent#soft-deleting
     *
     * @param  int  $id
     * @param  boolean $force
     * @return bool
     */
    public function delete($id, $force = false)
    {
        $register =  $this->read($id);
        if ($force == true) {
            return $register->forceDelete();
        } else {
            return $register->delete();
        }
    }
}
