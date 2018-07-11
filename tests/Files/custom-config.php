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
        ],

        'users-permissions' => [
            'edit' => 'acl::users-permissions.edit',
        ],
        
        'groups' => [
            'index'            => 'acl::groups.index',
            'create'           => 'acl::groups.create',
            'edit'             => 'acl::groups.edit',
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
    | 'users' => [                                    <-- A slug da função
    |    'label'       => 'Usuários',                 <-- O nome para exibição da função
    |    'permissions' => 'create,edit,show,delete',  <-- As habilidades configuráveis
    | ],
    */

    'roles' => [

        'posts' => [
            'label' => 'Postagens',
            'permissions' => 'create,edit,show',
            ],

        'category' => [
            'label' => 'Categorias',
            'permissions' => 'create',
            ],

        'tag' => [
            'label' => 'tags',
            'permissions' => 'edit',
            ],

        'gallery' => [
            'label' => 'Galeria de Fotos',
            'permissions' => 'show',
            ],

        'media' => [
            'label' => 'Gerenciador de Mídia',
            'permissions' => 'create,edit,show,delete',
            ],



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