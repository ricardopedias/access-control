<?php
namespace Laracl;

use Laracl\Services;
use Gate;

class Core
{
    /** @var array */
    protected static $debug = [];

    /** @var boolean */
    protected static $registered = false;

    /** @var array */
    protected static $policies = [];

    /**
     * Devolve as informações das funções de acesso regotsradas.
     *
     * @return array
     */
    public static function getPolicies()
    {
        return self::$policies;
    }

    public static function setPolice(string $role, string $permission)
    {
        self::$policies[] = (object) [
            'role'       => $role,
            'permission' => $permission
        ];
    }

    /**
     * Carrega e registra as diretivas para o blade
     *
     * @return void
     */
    public static function loadBladeDirectives()
    {
        include(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'directives.php');
    }

    public static function getDebug($param = null)
    {
        if ($param == null) {
            $value = self::$debug;
            self::$debug = [];
        } else {
            $value = self::$debug[$param] ?? null;
            self::$debug[$param] = null;
        }

        return $value;
    }

    public static function setDebug($param, $value)
    {
        self::$debug[$param] = $value;
    }

    public static function resetCore()
    {
        self::$debug = [];
        self::$registered = false;
    }

    /**
     * Salva os dados da última verificação por privilégios
     * Na verificação: users.edit = {$role}.{$permission}
     *
     * @param string $role
     * @param string $permission
     * @param boolean $granted
     */
    public static function traceCurrentAbility(string $role, string $permission, bool $granted)
    {
        self::$debug['current_ability'] = [
            'role'       => $role,
            'permission' => $permission,
            'granted'    => $granted,
            ];
    }

    /**
     * Salva a origem da última verificação por privilégios.
     * Útil para verificações de debug e testes de unidade.
     *
     * @param string $origin Possibilidades: config, callback, user, group
     */
    public static function traceCurrentAbilityOrigin(string $origin)
    {
        self::$debug['current_ability_origin'] = $origin;
    }

    /**
     * Salva os dados dos registros de funções de acesso
     *
     * @param string $role
     * @param string $permission
     */
    public static function traceRegisteredPolices(string $role, string $permission)
    {
        if(isset(self::$debug['registered_polices']) == false) {
            self::$debug['registered_polices'] = [];
        }

        self::$debug['registered_polices'][] = [
            'role'       => $role,
            'permission' => $permission
        ];
    }

    /**
     * Gera a estrutura de nomeamento de rotas para os CRUDs,
     * com base nas urls especificadas na configuração.
     *
     * Por exemplo:
     *
     * 'routes'     => [
     *      'users'              => 'painel/users',
     *      'users-permissions'  => 'painel/users-permissions',
     *      'groups'             => 'painel/groups',
     *      'groups-permissions' => 'painel/groups-permissions',
     * ]
     *
     * No item ['users' => 'painel/users'], serão extraidos
     * os indices e os nomes para as rotas dos CRUDs, ficando assim:
     * [
     *     laracl.routes.users.base   =>  'painel/users'
     *     laracl.routes.users.index  => 'users.index'
     *     laracl.routes.users.create => 'users.create'
     *     laracl.routes.users.store  => 'users.store'
     *     laracl.routes.users.edit   => 'users.edit'
     *     laracl.routes.users.update => 'users.update'
     *     laracl.routes.users.delete => 'users.delete'
     *     laracl.routes.users.destroy => 'users.destroy'
     *     laracl.routes.users.restore => 'users.restore'
     * ]
     */
    public static function normalizeConfig()
    {
        $config = config('laracl');

        // A configuração só pode ser normalizada uma vez
        // se a primeira rota já for um array, encerra a operação
        $first_route = current($config['routes']);
        if (is_array($first_route)) {
            return false;
        }

        foreach ($config['routes'] as $slug => $nulled) {

            // admin/users -> 'users'
            $route_base = preg_replace('#.*/#', '', $config['routes'][$slug]);

            $route_params = [
                "laracl.routes.{$slug}.base"    => $config['routes'][$slug],
                "laracl.routes.{$slug}.index"   => $route_base . ".index",
                "laracl.routes.{$slug}.create"  => $route_base . ".create",
                "laracl.routes.{$slug}.store"   => $route_base . ".store",
                "laracl.routes.{$slug}.edit"    => $route_base . ".edit",
                "laracl.routes.{$slug}.update"  => $route_base . ".update",
                "laracl.routes.{$slug}.delete"  => $route_base . ".delete",
                "laracl.routes.{$slug}.destroy" => $route_base . ".destroy",
                "laracl.routes.{$slug}.trash"   => $route_base . ".trash",
                "laracl.routes.{$slug}.restore" => $route_base . ".restore",
            ];
            config($route_params);
        }

        return true;
    }

    /**
     * Devolve as permissões para o usuário na função de acesso especificada
     * O formato procede assim: users.edit = {$role_slug}.edit

     * @param  int $user_id
     * @param  string $role_slug
     * @return Collection
     */
    public static function getUserPermissions($user_id, string $role_slug)
    {
        return (new Services\UsersPermissionsService)
            ->getPermissionsByUserID($user_id, $role_slug);
    }

    /**
     * Verifica se o usuário tem direito a executar a função de acesso
     * @param  int    $user_id
     * @param  string $role
     * @param  string $permission
     * @param  callable $callback
     * @return bool
     */
    public static function userCan(int $user_id, string $role, string $permission, $callback = null) : bool
    {
        return (new Services\UsersService)
            ->userCan($user_id, $role, $permission, $callback);
    }

    /**
     * Registra os verificadores de acesso com base na configuração
     *
     * @return bool
     */
    public static function registerPolicies()
    {
        if (self::$registered == true) {
            // As funções de acesso são registradas apenas uma vez
            return false;
        }

        $roles_list = config('laracl.roles');

        if (is_array($roles_list) == false || count($roles_list) == 0) {
            throw new \OutOfRangeException("You need to add the 'roles' in the Laracl configuration");
        }

        foreach ($roles_list as $role => $info) {

            $label = $info['label'];

            if (isset($info['permissions']) == false || is_string($info['permissions']) == false) {
                throw new \InvalidArgumentException(
                    "You need to add comma separated 'permissions' in the Laracl '{$role}' configuration");
            }

            $allowed_permissions = explode(',', trim($info['permissions'], ',') );
            foreach ($allowed_permissions as $permission) {

                $valid_permissions = ['create', 'read', 'update', 'delete'];
                if (in_array($permission, $valid_permissions) == false) {
                    throw new \UnexpectedValueException(
                        "Invalid permission '$permission'. The accepted values are: " . implode(',', $valid_permissions));
                }

                self::traceRegisteredPolices($role, $permission);

                self::setPolice($role, $permission);

                Gate::define("{$role}.{$permission}",
                    function ($user, $callback = null)
                    use ($role, $permission)
                {
                    return \Laracl\Core::userCan($user->id, $role, $permission, $callback);
                });
            }
        }

        self::$registered = true;
        return true;
    }

    public static function isTrash()
    {
        $nodes = explode('/', request()->path());
        $last_node = current(array_values(array_slice($nodes, -1)));
        return ($last_node === 'trash');
    }
}
