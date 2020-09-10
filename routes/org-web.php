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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/actas_pleno', 'HomeController@actaspleno')->name('actas_pleno');
Route::get('/actas_comisiones', 'HomeController@actascomis');

Route::get('ver_pdf/{path}', 'HomeController@ver_pdf')->name('ver_pdf');
Route::get('showAsambleaPdf/{path},{comi_pleno}', 'HomeController@showAsambleaPdf')->name('showAsambleaPdf');


