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


Route::resource('/event', EventsTypesController::class);

Route::post('/logevent', [EventController::class, 'store']);   //done
Route::post('/logevent/filter', [EventController::class, 'filter']);//done

Route::post('/product', [ProductCatalogController::class, 'store']); //done
Route::get('/product/{product_id}', [ProductCatalogController::class, 'index']);//done

Route::get('/test', [testingController::class, 'index']);
