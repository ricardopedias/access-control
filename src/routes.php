<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Route::middleware(['web', 'auth'])->group(function () {

    $config = config('laracl');

    $controller = !isset($config['controller']) || $config['controller']=='default' 
        ? "Laracl\Http\Controllers\PermissionsController"
        : $config['controller'];

    $route = !isset($config['route']) || $config['route']=='default' 
        ? "admin/users-permissions"
        : $config['route'];

    $route_base = explode('/', $route);
    $route_base = array_pop($route_base);

    config([
        'laracl.controller'          => $controller,
        'laracl.route'               => $route,
        'laracl.routes.perms_edit'   => $route_base . ".edit",
        'laracl.routes.perms_update' => $route_base . ".update"
        ]);

    Route::resource($route, $controller);
});