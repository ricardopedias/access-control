<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Modo paleativo para testes
if(env('APP_ENV') !== 'testing') {

    if (\Schema::hasTable('users') == false || \App\User::find(1) == null) {

        \Artisan::call('migrate');
        \Artisan::call('migrate', ['--path' => 'vendor/plexi/laracl/src/database/migrations']);

        $u = (new \Laracl\Services\UsersService)->dataInsert([
            'name'     => 'Ricardo',
            'email'    => 'ricardo@bnw.com.br',
            'password' => bcrypt('secret')
        ]);
    }

    Auth::loginUsingId(1);
}

Route::middleware(['web', 'auth'])->group(function () {

    $config = config('laracl');

    // Usuários, Grupos e Permissões
    foreach ($config['routes'] as $slug => $url) {

        $route      = $config['routes'][$slug]['base'];
        $controller = $config['controllers'][$slug];

        // Rotas extras da lixeira
        // Ex: usuarios.trash, usuarios.restore
        // Nota: devem ter precedencia em relação ao Route::resource
        // https://laravel.com/docs/5.6/controllers#restful-supplementing-resource-controllers
        $route_trash_url = $route . '/trash';
        $route_trash_name = $config['routes'][$slug]['trash'];
        Route::get($route_trash_url, $controller . '@trash')->name($route_trash_name);

        $route_restore_url = $route . '/trash/{id}';
        $route_restore_name = $config['routes'][$slug]['restore'];
        Route::post($route_restore_url, $controller . '@restore')->name($route_restore_name);

        // Rotas de resource padrão
        // Ex: usuarios.index, usuarios.create, usuarios.store
        // usuarios.edit, usuarios.update, usuarios.delete, usuarios.destroy
        Route::resource($route, $controller);
    }

});
