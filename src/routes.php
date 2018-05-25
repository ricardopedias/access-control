<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Auth::loginUsingId(1);

Route::middleware(['web', 'auth'])->group(function () {

    $config = config('laracl');

    // Usuários, Grupos e Permissões
    foreach ($config['routes'] as $slug => $nulled) {

        $route = $config['routes'][$slug]['base'];
        $controller = $config['controllers'][$slug];
        Route::resource($route, $controller);
    }

});
