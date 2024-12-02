<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
public function index()
    {
        return view('index');
    }

public function create()
    {
    // 全てのカテゴリーを取得
    $categories = Category::all();

    // 出品画面にカテゴリー情報を渡す
    return view('create', compact('categories'));
    }

public function store(ExhibitionRequest $request)
    {
        // 商品を作成
        $item = new Item();
        $item->name = $validatedData['name'];
        $item->price = $validatedData['price'];
        $item->category_id = $validatedData['category_id'];
        $item->description = $validatedData['description'];

        // 画像がアップロードされている場合は保存
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/item_images');
            $item->image_url = basename($imagePath);
        }

        $item->save();

        // 商品を保存後、一覧ページなどへリダイレクト
        return redirect()->route('profile.show');
    }

public function show()
    {
        return view('show');
    }

public function purchase()
    {
        return view('confirm');
    }

public function update()
    {
        return view('address');
    }
}



