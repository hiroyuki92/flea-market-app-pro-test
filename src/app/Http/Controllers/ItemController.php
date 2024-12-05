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
        $items = Item::all();
        /* foreach ($items as $item) {
        dd($item->image_url);  // 画像を表示する
    } */
        return view('index',compact('items'));
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
    // 画像名を事前に初期化
    $imageName = null;

    // 画像がアップロードされている場合
    if ($request->hasFile('image')) {
        // 画像を保存し、ファイル名を取得
        $imagePath = $request->file('image')->store('item_images', 'public');
        $imageName = basename($imagePath);  // ストレージに保存されたファイル名を取得
    } else {
        // 画像がアップロードされていない場合にエラーを表示する
        return back()->withErrors(['image' => '商品画像は必須です。']);
    }
    $itemData = array_merge(
        $request->only([
            'category_id',
            'name',
            'brand',
            'price',
            'description',
            'condition',
        ]),
        [
            'user_id' => auth()->id(),  // ログインしているユーザーのIDを追加
            'image_url' => $imageName,
        ]
    );

/*     dd($itemData);  // 保存されるデータを確認 */
Item::create($itemData);

    // 商品を保存後、プロフィールページなどにリダイレクト
    return redirect()->route('profile.show');  // 出品後はプロフィールページにリダイレクト
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



