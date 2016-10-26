<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@index');
Route::get('/get/url', 'HomeController@getUrl');
Route::get('/add/url', 'HomeController@addUrl');
Route::get('/add/comment', 'HomeController@addComment');
Route::get('/change/comment', 'HomeController@changeComment');
Route::get('/remove/comment', 'HomeController@removeComment');

