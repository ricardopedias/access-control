<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */
    // 'name'       => 'Permissões',
    // 'route'      => 'painel/permissions',
    // 'controller' => 'App\Http\Controllers\PermissionsController',

    'name'       => 'Permissões',
    'route'      => 'painel/permissions',
    'controller' => 'default',

    'roles' => [

        'guests' => [
            'label' => 'Visitantes',
            'permissions' => 'create,edit,show,delete',
            ],

        'admins' => [
            'label' => 'Administradores',
            'permissions' => 'create,edit,show,delete',
            ],

        'roots' => [
            'label' => 'Super Usuários',
            'permissions' => 'create,edit,show,delete',
            ],

        'shipping-companies' => [
            'label' => 'Transportadoras',
            'permissions' => 'create,edit,show',
            ],

        'users' => [
            'label' => 'Usuários',
            'permissions' => 'create,edit,show'
            ],

        'permissions' => [
            'label' => 'Permissões',
            'permissions' => 'edit,show'
            ],
    ]
];