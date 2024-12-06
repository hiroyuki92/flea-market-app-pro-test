<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
public function index()
    {
        $items = Item::all();
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

    Item::create($itemData);

    // 商品を保存後、プロフィールページなどにリダイレクト
    return redirect()->route('profile.show');  // 出品後はプロフィールページにリダイレクト
}

public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('show', compact('item'));
    }

public function toggleLike($itemId)
{
    try {
        $user = auth()->user();
        $item = Item::findOrFail($itemId);
        
        $existingFavorite = Favorite::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->first();
        
        $favorited = false;
        
        if ($existingFavorite) {
            // すでにいいねしている場合は削除
            $existingFavorite->delete();
        } else {
            // いいねしていない場合は追加
            Favorite::create([
                'user_id' => $user->id,
                'item_id' => $item->id
            ]);
            $favorited = true;
        }
        
        // 常に最新のいいね数を取得
        $favoritesCount = $item->favorites()->count();
        
        return response()->json([
            'favorited' => $favorited,
            'favoritesCount' => max(0, $favoritesCount) // マイナスにならないよう保証
        ]);
    } catch (\Exception $e) {
        \Log::error('Toggle like error: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'An error occurred',
            'message' => $e->getMessage()
        ], 500);
    }
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



