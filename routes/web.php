<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');
Route::post('/getCar','SearchController@index');
Route::post('/fill-select','SearchController@fillSelect');
Route::post('/reset','SearchController@reset');
Route::post('/showSelected','SearchController@showSelected');
Route::get('/show/{id}','SearchController@show');
Route::get('show-part/{id}','SearchController@showPart');
Route::get('showVPart/{id}','SearchController@showVehiclePart');
Route::get('showPartVehicle/{id}','SearchController@showPartVehicle');
