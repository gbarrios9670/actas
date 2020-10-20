<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/cargar_datos', 'HomeController@cargar_datos');

Route::get('/actas_pleno', function() {
  // $cadena = iconv('ISO-8859-1','UTF-8//TRANSLIT','De la Mujer, La NiÒez, La Juventud y La Familia');
  // return utf8_decode($cadena); 

  // return $cadena; 

  // $cadena = utf8_encode("Mañana toca programación");
  // return $cadena; 

  // The selected record showed "CASTA�O"
  // Using utf8-decode showed "CASTA?O"
  // Using utf8_encode shows the proper string "CASTAÑO"
//   $cadena = mb_detect_encoding('PoblaciÛn');
// return utf8_decode('PoblaciÛn');

//   $cadena = utf8_decode('De la Mujer, La NiÒez, La Juventud y La Familia'); 
//  return utf8_encode($cadena);

  return view('actaspleno');
});

Route::get('/actas_comisiones', function() {
  return view('actascomis');
});

Route::get('ver_pdf/{path}', 'HomeController@ver_pdf')->name('ver_pdf');
Route::get('showAsambleaPdf/{path},{comi_pleno}', 'HomeController@showAsambleaPdf')->name('showAsambleaPdf');

Route::get('/import_txt', 'HomeController@import_txt');
