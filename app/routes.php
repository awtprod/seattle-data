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
Route::get('predict', [
	'as' => 'data.predict',
	'uses' => 'DataController@predict'
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
Route::get('data/average', [
	'as' => 'data.average',
	'uses' => 'DataController@average'
]);
Route::post('data/predict_get', [
	'as' => 'data.predict_get',
	'uses' => 'DataController@predict_get'
]);
Route::post('data/average_get', [
	'as' => 'data.average_get',
	'uses' => 'DataController@average_get'
]);
Route::get('data/average_get', [
	'as' => 'data.average_get',
	'uses' => 'DataController@average_get'
]);
Route::get('data/median', [
	'as' => 'data.median',
	'uses' => 'DataController@calculate_median'
]);
Route::get('data/airport', [
	'as' => 'data.airport',
	'uses' => 'DataController@airport'
]);