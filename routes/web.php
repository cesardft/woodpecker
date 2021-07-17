<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return  $router->app->version();
});

$router->post('/login/{provider}', ['as' => 'authenticate', 'uses' => 'AuthenticateController@postAuthenticate']);

$router->get('/users/user', ['as' => 'user', 'uses' => 'UserController@getUser']);

$router->post('/transaction', ['as' => 'transaction', 'uses' => 'TransactionController@postTransaction']);
