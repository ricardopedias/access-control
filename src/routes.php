<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Route::middleware(['web', 'auth'])->group(function () {

    $config = config('laracl');

    $route = $config['route']=='default' 
        ? "admin/users-permissions"
        : $config['route'];

    $controller = $config['controller']=='default' 
        ? "Laracl\Http\Controllers\PermissionsController"
        : $config['controller'];

    config([
        'laracl.route'      => $route,
        'laracl.controller' => $controller,
        ]);

    Route::resource($route, $controller);
});