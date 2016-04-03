<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    View::make('welcome'); // will return app/views/index.php
});

//// API ROUTES ==================================
Route::group(array('prefix' => 'api'), function() {
    Route::resource('login', 'Auth\AuthenticateController@authenticate', array('only' => array('store')));
    Route::resource('register', 'Auth\AuthenticateController@register', array('only' => array('store')));
    Route::resource('activate', 'Auth\AuthenticateController@validateEmail', array('only' => array('store')));
    Route::resource('loginUser', 'Auth\AuthenticateController@getAuthenticatedUser', array('only' => array('index')));
    Route::resource('modules', 'Modules\ModuleController', array('only' => array('index')));

    // Test routes turn off when not needed
//    Route::resource('projects', 'Projects\ProjectController', array('only' => array('index', 'store', 'show', 'update', 'destroy')));
});

Route::group(array('prefix' => 'api', 'middleware' => 'jwt.auth'), function() {
    // User Routes
    Route::resource('users', 'Users\UserController', array('only' => array('index', 'store', 'show', 'update', 'destroy')));
    Route::resource('setActiveStatus', 'Users\UserController@setActiveStatus', array('only' => array('update')));
    Route::resource('findUsers', 'Users\UserController@findUsers', array('only' => array('show')));

    // Group Routes
    Route::resource('groups', 'Groups\GroupController', array('only' => array('index', 'store', 'show', 'update', 'destroy')));
    Route::resource('addUserToGroup', 'Groups\GroupController@addUserToGroup', array('only' => array('update')));
    Route::resource('removeUserFromGroup', 'Groups\GroupController@removeUserFromGroup', array('only' => array('update')));
    Route::resource('addProjectToGroup', 'Groups\GroupController@addProjectToGroup', array('only' => array('update')));
    Route::resource('removeProjectFromGroup', 'Groups\GroupController@removeProjectFromGroup', array('only' => array('update')));

    // Project Routes
    Route::resource('projects', 'Projects\ProjectController', array('only' => array('index', 'store', 'show', 'update', 'destroy')));
    Route::resource('deployProject', 'Projects\ProjectController@deployProject', array('only' => array('show')));
    Route::resource('findProjects', 'Projects\ProjectController@findProjects', array('only' => array('show')));
});
