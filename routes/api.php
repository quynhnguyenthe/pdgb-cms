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
    Route::get('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
    Route::get('/user-profile', [App\Http\Controllers\Api\AuthController::class, 'userProfile']);
    Route::post('/change-pass', [App\Http\Controllers\Api\AuthController::class, 'changePassWord']);

    Route::group([
        'prefix' => 'request'
    ], function ($router) {
        Route::get('/list-create', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'listCreate']);
        Route::get('/list-delete', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'listDelete']);
        Route::post('/review-registration/{id}', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'reviewRegistration']);
        Route::post('/review-deletion/{id}', [App\Http\Controllers\Api\Cms\ClubRequestController::class, 'reviewDeletion']);
    });

    Route::group([
        'prefix' => 'club'
    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\Cms\ClubController::class, 'getClubs']);
        Route::get('/detail/{id}', [App\Http\Controllers\Api\Cms\ClubController::class, 'detail']);
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

    Route::group([
        'prefix' => 'match'
    ], function ($router) {
        Route::get('/list', [App\Http\Controllers\Api\Cms\MatchController::class, 'list']);
    });

});

Route::group([
    'middleware' => 'google.api',
    'prefix' => 'user'
], function ($router) {
    Route::get('/user-info', [App\Http\Controllers\Api\User\MemberController::class, 'userInfo']);

    Route::group([
        'prefix' => 'request'
    ], function ($router) {
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
        Route::get('/detail', [App\Http\Controllers\Api\User\ClubController::class, 'detail']);
        Route::get('/list-other', [App\Http\Controllers\Api\User\ClubController::class, 'listOther']);
        Route::post('/request-join', [App\Http\Controllers\Api\User\MemberRequestController::class, 'requestJoin']);
        Route::post('/review-request-join/{id}', [App\Http\Controllers\Api\User\MemberRequestController::class, 'reviewRequestJoin']);
        Route::get('/list-member-request/', [App\Http\Controllers\Api\User\MemberRequestController::class, 'listRequestJoin']);
        Route::post('/cancel-request-join/{id}', [App\Http\Controllers\Api\User\MemberRequestController::class, 'cancelRequestJoin']);
        Route::get('/check', [App\Http\Controllers\Api\User\ClubController::class, 'check']);
    });

    Route::group([
        'prefix' => 'match'
    ], function ($router) {
        Route::post('/create', [App\Http\Controllers\Api\User\MatchController::class, 'create']);
        Route::get('/list-pk', [App\Http\Controllers\Api\User\MatchController::class, 'listPK']);
        Route::get('/list-match', [App\Http\Controllers\Api\User\MatchController::class, 'listMatch']);
    });
});
