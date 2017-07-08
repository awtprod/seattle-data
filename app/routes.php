<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::resource('locations', 'LocationsController');

Route::get('tokens/update', [
	'as' => 'tokens.update',
	'uses' => 'TokensController@update'
]);

Route::get('collect', [
	'as' => 'data.store',
	'uses' => 'DataController@store'
]);
Route::get('live', [
	'as' => 'data.show',
	'uses' => 'DataController@show'
]);
Route::get('data/live', [
	'as' => 'data.live',
	'uses' => 'DataController@live'
]);
Route::get('data', [
	'as' => 'data.index',
	'uses' => 'DataController@index'
]);
Route::get('data/avg', [
	'as' => 'data.avg',
	'uses' => 'DataController@average'
]);
Route::get('data/specific', [
	'as' => 'data.specific',
	'uses' => 'DataController@specific'
]);
Route::post('data/specific_get', [
	'as' => 'data.specific_get',
	'uses' => 'DataController@specific_get'
]);