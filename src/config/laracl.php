<?php

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
    | Rotas
    |--------------------------------------------------------------------------
    |
    | Estas são as rotas usadas pelos CRUS's do mecanismo de configuração de 
    | permissões. Para adequer as rotas ao seu projeto do Laravel, 
    | basta especificá-las aqui.
    */

    'routes'     => [
        'users'              => 'laracl/users',
        'users-permissions'  => 'laracl/users-permissions',
        'groups'             => 'laracl/groups',
        'groups-permissions' => 'laracl/groups-permissions', 
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
    | class PersonalController extends Laracl\Http\Controllers\UsersController {
    |    ...
    | }
    */

    'controllers'     => [
        'users'              => 'Laracl\Http\Controllers\UsersController',
        'users-permissions'  => 'Laracl\Http\Controllers\UsersPermissionsController',
        
        'groups'             => 'Laracl\Http\Controllers\GroupsController',
        'groups-permissions' => 'Laracl\Http\Controllers\GroupsPermissionsController',
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
            'index'             => 'laracl::users.index',
            'create'            => 'laracl::users.create',
            'edit'              => 'laracl::users.edit',
        ],

        'users-permissions' => [
            'edit' => 'laracl::users-permissions.edit',
        ],
        
        'groups' => [
            'index'            => 'laracl::groups.index',
            'create'           => 'laracl::groups.create',
            'edit'             => 'laracl::groups.edit',
        ],

        'groups-permissions' => [
            'edit' => 'laracl::groups-permissions.edit',
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
    | 'users' => [                                    <-- A slug da função
    |    'label'       => 'Usuários',                 <-- O nome para exibição da função
    |    'permissions' => 'create,edit,show,delete',  <-- As habilidades configuráveis
    | ],
    */

    'roles' => [

        'users' => [
            'label' => 'Usuários',
            'permissions' => 'create,edit,show,delete',
            ],

        'users-permissions' => [
            'label' => 'Permissões de Usuários',
            'permissions' => 'create,edit,show',
            ],

        'groups' => [
            'label' => 'Grupos de Acesso',
            'permissions' => 'create,edit,show,delete',
            ],

        'groups-permissions' => [
            'label' => 'Permissões de Grupos',
            'permissions' => 'create,edit,show',
            ],
    ]
];