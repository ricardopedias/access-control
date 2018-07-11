<?php
namespace Acl\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;

trait HasCompositePrimaryKey
{
    /**
     * Valor que indica se os IDs são auto-incrementais.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Seta as chaves para as consultas de atualização e gravação.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        foreach ($this->getKeyName() as $key) {

            if (isset($this->$key)) {
                $query->where($key, '=', $this->$key);
            }
            else {
                throw new Exception(__METHOD__ . 'Missing part of the primary key: ' . $key);
            }
        }
        return $query;
    }

    /**
     * Executa uma consulta para um único registro por ID.
     *
     * @param  array  $ids Array de chaves no formato [column => value].
     * @param  array  $columns
     * @return mixed|static
     */
    public static function find($ids, $columns = ['*'])
    {
        $me = new self;
        $query = $me->newQuery();
        foreach ($me->getKeyName() as $index => $key) {
            $value = $ids[$key] ?? $ids[$index];
            $query->where($key, '=', $value);
        }
        return $query->first($columns);
    }
}
