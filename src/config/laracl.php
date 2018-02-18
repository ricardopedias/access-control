<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Nome do Formulário
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name'       => 'Laracl',

    /*
    |--------------------------------------------------------------------------
    | Usuário com permissão total
    |--------------------------------------------------------------------------
    |
    | Este valor define o ID do usuário que sempre terá permissão especial.
    | O usuário setado aqui terá acesso total.
    */

    'root_user'       => 0,

    /*
    |--------------------------------------------------------------------------
    | Rota Base
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'routes'     => [
        'users'              => 'admin/users',
        'users-permissions'  => 'admin/users-permissions',
        'groups'             => 'admin/groups',
        'groups-permissions' => 'admin/groups-permissions', 
    ],

    /*
    |--------------------------------------------------------------------------
    | Rota Base
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'controllers'     => [
        'users'              => 'Laracl\Http\Controllers\UsersController',
        'users-permissions'  => 'Laracl\Http\Controllers\UsersPermissionsController',
        
        'groups'             => 'Laracl\Http\Controllers\GroupsController',
        'groups-permissions' => 'Laracl\Http\Controllers\GroupsPermissionsController',
    ],

    /*
    |--------------------------------------------------------------------------
    | Visão
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
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
    | Funções disponíveis
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
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