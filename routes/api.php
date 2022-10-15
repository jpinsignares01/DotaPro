<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PersonasApiController;
use App\Http\Controllers\Api\V1\DispositivosApiController;

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

Route::group([
    'prefix' => 'v1'
], function (){
    //Personas
    Route::get('personas', [PersonasApiController::class, 'index'])->name('api.personas_index');

    //Dispotivos
    Route::group([
        'prefix' => 'dispositivos'
    ], function (){
        Route::get('/{personas_id}', [DispositivosApiController::class, 'index'])->name('api.index_dispositivo');
        Route::post('/desvincular', [DispositivosApiController::class, 'desvincular'])->name('api.desvincular_dispositivo');
        Route::post('/asignar', [DispositivosApiController::class, 'asignar'])->name('api.asignar_dispositivo');
    });
});