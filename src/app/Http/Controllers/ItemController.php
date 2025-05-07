<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // 入力されたキーワードを取得
        $keyword = $request->input('keyword', '');
        $items = Item::keywordsearch($keyword)
            ->where('user_id', '!=', Auth::id())  // 自分が出品した商品を除外
            ->get();

        // マイリスト（いいねした商品）の取得
        $myListItems = collect(); // デフォルトは空のコレクション
        if (Auth::check()) {
            $myListItems = Auth::user()->favorites()
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                })
                ->get();
        }

        $queryParams = $request->only(['keyword']);

        return view('index', compact('items', 'myListItems', 'keyword', 'queryParams'));
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
        $imageName = null;

        // 画像がアップロードされている場合
        if ($request->hasFile('image')) {
            // 画像を保存し、ファイル名を取得
            $imagePath = $request->file('image')->store('item_images', 'public');
            $imageName = basename($imagePath);
        } else {
            return back()->withErrors(['image' => '商品画像は必須です。']);
        }
        $itemData = array_merge(
            $request->only([
                'name',
                'brand',
                'price',
                'description',
                'condition',
            ]),
            [
                'user_id' => auth()->id(),
                'image_url' => $imageName,
            ]
        );
        $item = Item::create($itemData);
        $categoryIds = explode(',', $request->input('category_ids'));
        $item->categories()->attach($categoryIds);

        return redirect()->route('profile.show');
    }

    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $comments = $item->comments()->with('user')->get();
        $commentsCount = $comments->count();

        return view('show', compact('item', 'comments', 'commentsCount'));
    }

    public function toggleLike($item_id)
    {
        try {
            $user = auth()->user();
            $item = Item::findOrFail($item_id);

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
                    'item_id' => $item->id,
                ]);
                $favorited = true;
            }

            // 常に最新のいいね数を取得
            $favoritesCount = $item->favorites()->count();

            return response()->json([
                'favorited' => $favorited,
                'favoritesCount' => max(0, $favoritesCount), // マイナスにならないよう保証
            ]);
        } catch (\Exception $e) {
            \Log::error('Toggle like error: '.$e->getMessage());

            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
