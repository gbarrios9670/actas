<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/cargar_datos', 'HomeController@cargar_datos');

Route::get('/actas_pleno', function() {
  return view('actaspleno');
});

Route::get('/actas_comisiones', function() {
  return view('actascomis');
});

Route::get('ver_pdf/{path}', 'HomeController@ver_pdf')->name('ver_pdf');
Route::get('showAsambleaPdf/{path},{comi_pleno}', 'HomeController@showAsambleaPdf')->name('showAsambleaPdf');

Route::get('/import_txt', 'HomeController@import_txt');
