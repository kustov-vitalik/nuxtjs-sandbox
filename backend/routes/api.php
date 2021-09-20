<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
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

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'auth',
], static function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(['auth:api']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh'])->withoutMiddleware(['auth:api']);
    Route::get('user', [AuthController::class, 'user']);
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'users'
], static function () {
    Route::get('', [UsersController::class, 'getList']);
    Route::post('', [UsersController::class, 'create']);
    Route::put('/{userId}', [UsersController::class, 'update']);
    Route::get('/{userId}', [UsersController::class, 'get']);
    Route::delete('/{userId}', [UsersController::class, 'delete']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
