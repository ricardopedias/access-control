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
    | Rota Base
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'route'      => 'admin/user-permissions',

    /*
    |--------------------------------------------------------------------------
    | Rota Base
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'controller' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Visão
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'view'       => 'default',

    /*
    |--------------------------------------------------------------------------
    | Componente
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'component'  => 'default',

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
    ]
];