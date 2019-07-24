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

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::group(['middleware'=>['auth:api']], function(){
	//Route::get('logout', 'API\UserController@logout');	
	Route::get('logout', 'API\UserController@logout');
});

Route::group(['middleware'=>['auth:api'],'prefix'=>'v1'], function(){
	Route::get('/user', function (Request $request) {
	    return $request->user();
	});

	Route::post('details', 'API\UserController@details');
	
	Route::get('/products', 'ProductController@index');
	Route::get('/product/{product}', 'ProductController@show');

	Route::get('/brands', 'BrandController@index');
});

