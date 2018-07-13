<?php
namespace Acl\Services;

use Acl\Core;

class RolesService
{
    /**
     * Devolve a estrutura completa das funções de acesso disponíveis
     * no arquivo de configuração que foram devidamente registradas.
     *
     * @return array
     */
    public function getStructure()
    {
        $structure = [];

        $abilities = config('acl.roles');

        // Habilidades registradas
        foreach (Core::getPolicies() as $item) {

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
