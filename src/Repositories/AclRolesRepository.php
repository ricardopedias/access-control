<?php
namespace Laracl\Repositories;

use Laracl\Models\AclRole;

class AclRolesRepository extends IRepository
{
    protected $model_class = AclRole::class;

    public function findBySlug($slug, bool $failable = false)
    {
        $info = config("laracl.roles.{$slug}");

        if (!isset($info) || !isset($info['label'])) {
            if($failable == true) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
            } else {
                return null;
            }
        }

        $role = $this->newQuery()->where('slug', $slug)->first();

        // Se a função de acesso nunca foi invocada, deve ser criada
        if ($role == null) {

            $this->create([
                'name'        => $info['label'],
                'slug'        => $slug,
                'description' => $info['description'] ?? ''
            ]);
        }

        $builder = $this->newQuery()->where('slug', $slug);
        if($failable == true) {
            return $builder->firstOrFail();
        } else {
            return $builder->first();
        }
    }

    /**
     * Devolve a estrutura completa das funções de acesso disponíveis
     * no arquivo de configuração que foram devidamente registradas.
     *
     * @return array
     */
    public function getStructure()
    {
        $structure = [];

        $abilities = config('laracl.roles');

        // Habilidades resistradas
        foreach (\Laracl\Core::getPolicies() as $item) {

            $role       = $item->role;
            $permission = $item->permission;

            if (isset($structure[$role]) == false) {
                $structure[$role] = [
                    'label' => $abilities[$role]['label']
                ];
            }

            if (isset($structure[$role]['permissions']) == false) {
                $structure[$role]['permissions'] = [
                    'create' => null,
                    'read'   => null,
                    'update' => null,
                    'delete' => null,
                ];
            }

            // Não nulos aparecerão no formulário
            // Nulos terão o checkbox ocultado
            $structure[$role]['permissions'][$permission] = '';
        }

        return $structure ?? [];
    }
}
