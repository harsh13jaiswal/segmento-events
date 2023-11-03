<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventsTypesController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\productCatalogController;
use App\Http\Controllers\testingController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/event', [EventsTypesController::class, 'create']);
Route::get('/event', [EventsTypesController::class, 'index']);
Route::get('/event/{id}', [EventsTypesController::class, 'show']);
Route::patch('/event/{id}', [EventsTypesController::class, 'update']);
Route::delete('/event/{id}', [EventsTypesController::class, 'destroy']);

Route::post('/logevent', [EventController::class, 'create']);
Route::post('/logevent/filter/{baseId}', [EventController::class, 'filter']);

Route::post('/product/{baseId}', [productCatalogController::class, 'create']);
Route::get('/product/{productId}', [productCatalogController::class, 'index']);

Route::get('/test', [testingController::class, 'index']);
