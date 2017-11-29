<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->match(['get','post'],'/douban/dislike', "GroupController@dislike");
    $router->match(['get','post'],'/douban/star', "GroupController@star");

    $router->resource('/douban', "GroupController");

    $router->resource('/douban_star', "GroupStarController");
});
