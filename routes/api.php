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

Route::get('/book', ['as' => 'book.list', 'uses' => 'BookController@index']);
Route::get('/book/{book}', ['as' => 'book.view', 'uses' => 'BookController@view']);
Route::post('/book/{book}/update', ['as' => 'book.update', 'uses' => 'BookController@update']);
Route::post('/book/{book}/delete', ['as' => 'book.delete', 'uses' => 'BookController@delete']);
Route::post('/book/create', ['as' => 'book.create', 'uses' => 'BookController@create']);
