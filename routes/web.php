<?php

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

Route::get('/', 'BlackjackGame@index');
Route::get('play', 'BlackjackGame@index');
Route::get('play/new_game', 'BlackjackGame@new_game');
Route::get('play/hit', 'BlackjackGame@hit');
Route::get('play/stand', 'BlackjackGame@stand');
Route::get('play/deal', 'BlackjackGame@deal');
