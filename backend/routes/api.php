<?php

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });




Route::group(['prefix' => 'product', 'middleware' => 'throttle:1000,10'], function () {



    Route::post('/store', [ProductController::class, 'store']);
    // Route::get('/show', [Product::class, 'show']);
    Route::delete('/delete/{id}', [Product::class, 'destroy']);
    Route::post('/update/{id}', [Product::class, 'update']);
});
