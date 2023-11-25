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
Route::post('/cms/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'cms'

], function ($router) {
//    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::get('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
    Route::get('/user-profile', [App\Http\Controllers\Api\AuthController::class, 'userProfile']);
    Route::post('/change-pass', [App\Http\Controllers\Api\AuthController::class, 'changePassWord']);

    Route::group([
        'prefix' => 'request'
    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'list']);
        Route::post('/review-registration/{id}', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'reviewRegistration']);
        Route::post('/review-deletion/{id}', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'reviewDeletion']);
    });

    Route::group([
        'prefix' => 'club'
    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\Cms\ClubController::class, 'getClubs']);
        Route::post('/create', [App\Http\Controllers\Api\Cms\ClubController::class, 'create']);
        Route::post('/refresh', [App\Http\Controllers\Api\Cms\ClubController::class, 'refresh']);
        Route::post('/change-pass', [App\Http\Controllers\Api\Cms\ClubController::class, 'changePassWord']);
    });

    Route::group([
        'prefix' => 'member'
    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\Cms\MemberController::class, 'list']);
        Route::post('/create', [App\Http\Controllers\Api\Cms\MemberController::class, 'create']);
        Route::get('/detail/{id}', [App\Http\Controllers\Api\Cms\MemberController::class, 'detail']);
    });
});

Route::group([
//    'middleware' => 'auth:api:user',
    'prefix' => 'user'

], function ($router) {

    Route::group([
        'prefix' => 'request'

    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\User\ClubRequestController::class, 'getClubs']);
        Route::post('/create', [App\Http\Controllers\Api\User\ClubRequestController::class, 'create']);
        Route::post('/delete', [App\Http\Controllers\Api\User\ClubRequestController::class, 'delete']);
    });

    Route::group([
        'prefix' => 'sport-discipline'

    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\User\SportsDisciplineController::class, 'list']);
    });

    Route::group([
        'prefix' => 'club'

    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\User\ClubController::class, 'list']);
        Route::get('/list-other', [App\Http\Controllers\Api\User\ClubController::class, 'listOther']);
        Route::post('/request-join', [App\Http\Controllers\Api\User\ClubController::class, 'requestJoin']);
        Route::post('/review-request-join/{id}', [App\Http\Controllers\Api\User\ClubController::class, 'reviewRequestJoin']);
        Route::get('/list_member_request/{id}', [App\Http\Controllers\Api\User\ClubController::class, 'listRequestJoin']);
    });
});
