<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', "NavidadController@inicio");
Route::post('/secreto', "NavidadController@RegistroAmigoSecreto")->name("amigo.secreto");
Route::post('/regalo', "NavidadController@RegistroRegalo")->name("regalo.registro");
Route::post('/regaloreserva', "NavidadController@ReservaRegalo")->name("regalos.reserva");
Route::get('/eliminarregalo/{id}', "NavidadController@eliminarregalo")->name("eliminar.regalo");
Route::get('/cancelarregalo/{id}', "NavidadController@cancelarreserva")->name("cancelar.regalo");
Route::get('/regalos-pdf', 'NavidadController@pdf')
    ->name('regalos.pdf');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
