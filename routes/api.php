<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
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

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::group(['prefix' => 'auth', 'namespace' => 'Api'], function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('user', 'AuthController@show');
    });
});

Route::group(['prefix' => 'realtime', 'namespace' => 'Api'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/contacts', 'RealtimeController@contacts');
        Route::get('/conversations', 'RealtimeController@conversations');
        Route::get('/converse-messages/{id}', 'RealtimeController@fetchConverseMessages');
        Route::post('/send-message', 'RealtimeController@sendMessage');
        Route::post('/receive-message/{id}', 'RealtimeController@receiveMessage');
        Route::post('/read-message/{id}', 'RealtimeController@readMessage');
    });
});
