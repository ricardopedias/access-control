<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Usuário com permissão total
    |--------------------------------------------------------------------------
    |
    | Este valor define o ID do usuário que sempre terá permissão especial.
    | O usuário setado aqui terá acesso total.
    */

    'root_user' => 1,

    /*
    |--------------------------------------------------------------------------
    | Modo de exclusão de registros
    |--------------------------------------------------------------------------
    |
    | Este valor define a forma como as exclusões serão efetuadas.
    | @see https://laravel.com/docs/5.6/eloquent#soft-deleting
    */

    'soft_delete' => 1,

    /*
    |--------------------------------------------------------------------------
    | Rotas
    |--------------------------------------------------------------------------
    |
    | Estas são as rotas usadas pelos CRUS's do mecanismo de configuração de
    | permissões. Para adequar as rotas ao seu projeto do Laravel,
    | basta especificar os valores aqui.
    */

    'routes'     => [
        'users'              => 'acl/users',
        'users-permissions'  => 'acl/users-permissions',
        'groups'             => 'acl/groups',
        'groups-permissions' => 'acl/groups-permissions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Controladores
    |--------------------------------------------------------------------------
    |
    | Os controladores podem ser personalizados, configurando-os nesta seção.
    | Uma forma limpa de implementar controladores personalizados é estendendo
    | suas funcionalidades originas e mudando apenas o necessário:
    |
    | Por exemplo:
    | class PersonalController extends Acl\Http\Controllers\UsersController {
    |    ...
    | }
    */

    'controllers'     => [
        'users'              => 'Acl\Http\Controllers\UsersController',
        'users-permissions'  => 'Acl\Http\Controllers\UsersPermissionsController',

        'groups'             => 'Acl\Http\Controllers\GroupsController',
        'groups-permissions' => 'Acl\Http\Controllers\GroupsPermissionsController',
    ],

    /*
    |--------------------------------------------------------------------------
    | Visões
    |--------------------------------------------------------------------------
    |
    | Os templates podem ser personalizados, configurando-os nesta seção.
    */

    'views' => [

        'users' => [
            'index'             => 'acl::users.index',
            'create'            => 'acl::users.create',
            'edit'              => 'acl::users.edit',
            'delete'            => 'acl::users.delete',
            'trash'             => 'acl::users.trash',
        ],

        'users-permissions' => [
            'edit' => 'acl::users-permissions.edit',
        ],

        'groups' => [
            'index'            => 'acl::groups.index',
            'create'           => 'acl::groups.create',
            'edit'             => 'acl::groups.edit',
            'delete'           => 'acl::groups.delete',
            'trash'            => 'acl::groups.trash',
        ],

        'groups-permissions' => [
            'edit' => 'acl::groups-permissions.edit',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Habilidades
    |--------------------------------------------------------------------------
    |
    | As habilidades gerenciáveis nos CRUD's do mecanismo de configuração de
    | permissões devem ser adicionadas aqui. Cada habilidade deve ser adicionada
    | com sua slug, seguida de dois parâmetros, sendo:
    |
    | 'users' => [                                      <-- A slug da função
    |    'label'       => 'Usuários',                   <-- O nome para exibição da função de acesso
    |    'permissions' => 'create,read,update,delete',  <-- As habilidades configuráveis
    | ],
    */

    'roles' => [

        'users' => [
            'label' => 'Usuários',
            'permissions' => 'create,read,update,delete',
            'description' => 'Gerenciamento de usuários'
            ],

        'users-permissions' => [
            'label' => 'Permissões de Usuários',
            'permissions' => 'create,read,update',
            'description' => 'Permissões de Usuários'
            ],

        'groups' => [
            'label' => 'Grupos de Acesso',
            'permissions' => 'create,read,update,delete',
            'description' => 'Grupos de Acesso'
            ],

        'groups-permissions' => [
            'label' => 'Permissões de Grupos',
            'permissions' => 'create,read,update',
            'description' => 'Permissões de Grupos'
            ],
    ]
];
