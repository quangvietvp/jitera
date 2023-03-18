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
    return $router->app->version();
});

Route::group(['prefix' => 'api'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('user-profile', 'AuthController@me');

    Route::post('follow/{username}', 'UserController@follow');
    Route::post('user/create', 'UserController@store');
    Route::post('user/update/{userId}', 'UserController@update');
    Route::post('user/delete/{userId}', 'UserController@destroy');
    Route::post('user/show/{userId}', 'UserController@show');
    Route::post('user/search', 'UserController@search');
    Route::post('user/follow', 'UserController@follow');
    Route::get('user/following', 'UserController@following');
    Route::post('user/unfollow', 'UserController@unfollow');
});