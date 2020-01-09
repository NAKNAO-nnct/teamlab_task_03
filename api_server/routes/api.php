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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::resource('product', 'ProductsController')->names([
//     'index' => 'product.hoge'
// ]);


Route::group(['prefix' => 'product'], function () {
    // 検索
    Route::get('', 'ProductsController@index');

    // 取得
    Route::get('/{id}', function ($id) {
        return;
    });

    // 全表示
    // Route::get('', function () {
    //     return;
    // });

    // 登録
    Route::post('', function () {
        return;
    });

    // 更新
    Route::put('/{id}', function ($id) {
        return;
    });

    // 削除
    Route::delete('/{id}', function ($id) {
        return;
    });
});
