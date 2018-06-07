<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['middleware' => ['jwt.auth']], function () {
   
    Route::get('users/my', ['as' => 'users.my', 'uses' => 'API\UserApiController@my']);
    Route::post('users/update', ['as' => 'users.update', 'uses' => 'API\UserApiController@update']);
});

Route::get('/auth/refresh', ['as' => 'auth.refresh', 'uses' => 'API\AuthApiController@refresh']);
Route::post('/register', ['as' => 'auth.register', 'uses' => 'API\UserApiController@register']);
Route::post('/login', ['as' => 'auth.login', 'uses' => 'API\UserApiController@login']);


Route::group(['prefix'=>'tasks'],function(){ /* messages Routes */
	Route::get('index', 'API\TaskApiController@index');
	Route::group(['middleware' => ['jwt.auth']], function () {
		
		Route::post('create','API\TaskApiController@create');
		//Route::post('edit','API\TodoApiController@edit'); /* Message crud */
		Route::post('update/{id}', 'API\TaskApiController@update');
		Route::post('delete/{id}', 'API\TaskApiController@delete');
	});
});