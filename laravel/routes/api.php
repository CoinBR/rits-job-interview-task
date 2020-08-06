<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$apiCRUDs = [
    'clientes', 
    'produtos',
];

Route::post('/clientes', 'ClientesController@store');
Route::get('/clientes/{cliente}', 'ClientesController@show');
Route::patch('/clientes/{cliente}', 'ClientesController@update');
Route::delete('/clientes/{cliente}', 'ClientesController@destroy');

Route::post('/produtos', 'ProdutosController@store');
Route::get('/produtos/{produto}', 'ProdutosController@show');
Route::patch('/produtos/{produto}', 'ProdutosController@update');
Route::delete('/produtos/{produto}', 'ProdutosController@destroy');

Route::post('/pedidos', 'PedidosController@store');
Route::get('/pedidos/{pedido}', 'PedidosController@show');
Route::patch('/pedidos/{pedido}', 'PedidosController@update');
Route::delete('/pedidos/{pedido}', 'PedidosController@destroy');
