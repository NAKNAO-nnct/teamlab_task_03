<?php

namespace App\Http\Controllers;

use App\Products;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\ProcessUtils;
use Symfony\Component\Process\Process;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requeset)
    {
        // 全データを取得
        $response = Products::all();

        // queryを取得
        $params = $requeset->all();

        // 最大価格
        if (!empty($params['max_price']))
            $response = $response->where('price', '<=', $params['max_price']);

        // 最小価格
        if (!empty($params['min_price']))
            $response = $response->where('price', '>=', $params['min_price']);

        // 名前検索
        if (!empty($params['name']))
            $response = $response->where('price', 'like', '%' . $params['name'] . '%');

        return response($response);
        // return response(Products::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // idの生成
        $id = count(Products::all()) + 1;

        // queryからデータ取得
        $name = $request->input('name');
        $description = $request->input('description');

        // 画像の保存
        if ($request->input('image') != null) {
            // 画像保存処理
            $image = $this->saveProductImages($request->input('image'), $id);

            // 画像の保存に失敗
            if ($image['ok'] == false) {
                return response([
                    'success' => 'failure',
                    'message' => 'エラー',
                    "details" => '画像の保存に失敗しました',
                    "details_url" => "https://example.jp/response"
                ]);
            }
            // 成功
            $image = $image['save_path'];
        } else {
            $image = null;
        }
        $price = $request->input('price');

        // DBに保存
        $response = Products::insert(
            [
                "id" => $id,
                "name" => $name,
                "description" => $description,
                "image" => $image,
                "price" => $price
            ]
        );

        return response(Products::find($id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // idの商品情報を表示
        return response(Products::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // 商品情報の更新

        // $product = new Products();
        // $product->fill($request->all());
        // $product->update();


        $product = Products::find($id);
        $query = $request->all();

        // 各項目uodate
        if (array_key_exists('description', $product)) {
            if ($query['description'] != null) {
                $product->name = $query['description'];
            }
        }
        if (array_key_exists('name', $product)) {
            if ($query['name'] != null) {
                $product->name = $query['name'];
            }
        }
        if (array_key_exists('price', $product)) {
            if ($query['price'] != null) {
                $product->name = $query['price'];
            }
        }
        if (array_key_exists('image', $product)) {
            if ($query['image'] != null) {
                if ($product['image'] != null) {
                    // 画像の削除処理
                    $image_path = $product['image'];
                    if ($image_path != null) {
                        $delete_response = $this->deleteProductImages($product['image']);
                        if ($delete_response['ok'] == 'failure') {
                            return response(
                                [
                                    'success' => 'failure',
                                    'message' => 'エラー',
                                    "details" => $delete_response['message'],
                                    "details_url" => "https://example.jp/response"
                                ]
                            );
                        }
                    }
                }
            }



            // 画像保存処理
            $image = $this->saveProductImages($query['image'], $id);

            // 画像の保存に失敗
            if ($image['ok'] == false) {
                return response([
                    'success' => 'failure',
                    'message' => 'エラー',
                    "details" => '画像の保存に失敗しました',
                    "details_url" => "https://example.jp/response"
                ]);
            }
            // 成功
            $product->image = $image['save_path'];
        }
        $this->destroy($product);

        Products::insert(
            $product
        );

        // $product->update();

        return response(Products::find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // idの商品を削除
        // idの商品情報を取得
        $product = Products::find($id);

        // 画像の削除処理
        $image_path = $product['image'];
        if ($image_path != null) {
            $delete_response = $this->deleteProductImages($product['image']);
            if ($delete_response['ok'] == 'failure') {
                return response(
                    [
                        'success' => 'failure',
                        'message' => 'エラー',
                        "details" => $delete_response['message'],
                        "details_url" => "https://example.jp/response"
                    ]
                );
            }
        }

        // DBから削除
        Products::find($id)->delete();
    }

    // 画像を削除
    public function deleteProductImages($file_name)
    {
        $file_path = __DIR__ . "/../../../public/" . $file_name;
        // ファイルの有無を確認
        if (file_exists($file_path) == False) {
            return array('ok' => 'success', 'message' => 'file not found');
        }
        // ファイルを削除
        if (unlink($file_path))
            return array('ok' => 'success', 'message' => '');
        return array('ok' => 'failure', 'message' => '削除に失敗しました');
    }


    // 画像を保存
    public function saveProductImages($images_base64, $id)
    {
        // base64データから画像ファイル化
        // base64デコード
        $images_base64 = base64_decode(explode(",", $images_base64)[1]);
        // ファイル種類から拡張子を取得
        $extension = explode("/", finfo_buffer(finfo_open(), $images_base64, FILEINFO_EXTENSION))[0];
        /* エラーチェックが必要*/
        // ファイル名（id.拡張子）
        $save_file_name = "${id}.${extension}";
        // ファイルの保存
        // 成功しなかった場合errorを返す
        if (file_put_contents(__DIR__ . "/../../../public/images/${save_file_name}", $images_base64)) {
            return array('ok' => True, 'save_path' => "/images/${save_file_name}");
        }
        return array('ok' => False);
    }
}
