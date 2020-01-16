<?php

use Illuminate\Http\Request;

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

Route::group(["middleware" => "guest:api"], function () {
    Route::post("/login", "ApiController@login");
});

Route::group(["middleware" => "auth:api"], function () {
    Route::get("/me", "ApiController@me");
});

Route::group(['middleware' => 'api'], function () {
    Route::group(['middleware' => 'jwt.auth', 'prefix' => 'product'], function () {
        // 検索
        Route::get('', 'ProductsController@index');

        // 取得
        Route::get('/{id}', 'ProductsController@show');

        // 登録
        Route::post('', 'ProductsController@store');

        // 更新
        Route::put('/{id}', 'ProductsController@update');

        // 削除
        Route::delete('/{id}', 'ProductsController@destroy');
    });
});
