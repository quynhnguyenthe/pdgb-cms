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

Route::group([
    'middleware' => 'api',
    'prefix' => 'cms'

], function ($router) {
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
//    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::get('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
    Route::get('/user-profile', [App\Http\Controllers\Api\AuthController::class, 'userProfile']);
    Route::post('/change-pass', [App\Http\Controllers\Api\AuthController::class, 'changePassWord']);

    Route::group([
        'prefix' => 'request'

    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'list']);
        Route::post('/review_registration/{id}', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'reviewRegistration']);
        Route::post('/review_deletion/{id}', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'reviewDeletion']);
    });

    Route::group([
        'prefix' => 'club'

    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\Cms\ClubController::class, 'getClubs']);
        Route::post('/create', [App\Http\Controllers\Api\Cms\ClubController::class, 'create']);
        Route::post('/refresh', [App\Http\Controllers\Api\Cms\ClubController::class, 'refresh']);
        Route::post('/change-pass', [App\Http\Controllers\Api\Cms\ClubController::class, 'changePassWord']);
    });
});

Route::group([
//    'middleware' => 'api',
    'prefix' => 'user'

], function ($router) {

    Route::group([
        'prefix' => 'request'

    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\User\ClubRequestController::class, 'getClubs']);
        Route::post('/create', [App\Http\Controllers\Api\User\ClubRequestController::class, 'create']);
    });
});
