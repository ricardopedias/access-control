<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// if (App\User::find(1) == null) {
//     $u = new App\User;
//     $u->name="Ricardo";
//     $u->email="ricardo@bnw.com.br";
//     $u->password = bcrypt('secret');
//     $u->save();
// }
// Auth::loginUsingId(1);

Route::middleware(['web', 'auth'])->group(function () {

    $config = config('laracl');

    // Usuários, Grupos e Permissões
    foreach ($config['routes'] as $slug => $nulled) {

        $route = $config['routes'][$slug]['base'];
        $controller = $config['controllers'][$slug];
        Route::resource($route, $controller);
    }

});
