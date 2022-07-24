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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/',function(){
    return "api測試成功";
});

// Route::group([
//     'middleware' => 'api',
//     'prefix' => 'admin'
// ], function ($router) {
//     Route::post('/registerAdmin', [App\Http\Controllers\AuthController::class, 'registerAdmin']);
// });

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/registerAdmin', [App\Http\Controllers\AuthController::class, 'registerAdmin']);
    Route::post('/loginAdmin', [App\Http\Controllers\AuthController::class, 'loginAdmin']);
    Route::post('/showAllAdmin', [App\Http\Controllers\AuthController::class, 'showAllAdmin']);

    Route::post('/accountCheck', [App\Http\Controllers\AuthController::class, 'accountCheck']);
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/refresh', [App\Http\Controllers\AuthController::class, 'refresh']);
    Route::get('/user-profile', [App\Http\Controllers\AuthController::class, 'userProfile']);
});

// Route::middleware('cors')->group(function (){
//     Route::get('/', function(){
//         return response()->json('跨域成功！',200);
//     });
// });
