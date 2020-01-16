<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Auth::routes();

Route::get('/', function () {
    return response([]);
});
// Route::get('/home', 'HomeController@index')->name('home');

// API
// Route::group(['prefix' => 'api/products'], function () {
//     // 検索
//     Route::get('/', function () {
//         return;
//     });

//     // 取得
//     Route::get('/{id}', function ($id) {
//         return;
//     });

//     // 全表示
//     Route::get('', function () {
//         return;
//     });

//     // 登録
//     Route::post('', function () {
//         return;
//     });

//     // 更新
//     Route::put('/{id}', function ($id) {
//         return;
//     });

//     // 削除
//     Route::delete('/{id}', function ($id) {
//         return;
//     });
// });
