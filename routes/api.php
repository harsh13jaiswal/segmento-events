<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventsTypesController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProductCatalogController;
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


Route::post('/event', [EventsTypesController::class, 'create']); //done
Route::get('/event', [EventsTypesController::class, 'index']);   //done
Route::get('/event/{id}', [EventsTypesController::class, 'show']); //done
Route::delete('/event/{id}', [EventsTypesController::class, 'destroy']);//done

Route::post('/logevent', [EventController::class, 'create']);   //done
Route::post('/logevent/filter', [EventController::class, 'filter']);//done

Route::post('/product/{base_id}', [ProductCatalogController::class, 'create']); //done
Route::get('/product/{product_id}', [ProductCatalogController::class, 'index']);//done

Route::get('/test', [testingController::class, 'index']);
