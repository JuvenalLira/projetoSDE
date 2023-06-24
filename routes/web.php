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

Route::get('/', function () {
    return view('welcome');
});



Route::prefix('app')->group(function(){
    

    Route::prefix('produto')
        ->middleware('auth')
        ->group(function() {

        Route::redirect('/', '/app/produto/listar');
        
        Route::get('/buscar', 'App\Http\Controllers\ProdutoController@search')->name('produto.buscar');

        Route::get('/cadastrar', 'App\Http\Controllers\ProdutoController@create')->name('produto.cadastrar');
        Route::post('/cadastrar', 'App\Http\Controllers\ProdutoController@store');

    });
});
