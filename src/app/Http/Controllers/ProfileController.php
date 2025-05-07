<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $tab = $request->input('tab', 'sell');
        $user = Auth::user();

        // データの取得
        $items = $tab === 'sell' ? $user->items : [];
        $purchases = $tab === 'buy' ? $user->purchases()->with('item')->get() : [];
        $transactions = $tab === 'transaction' ?
        $user->items()
            ->where('in_transaction', 1)
            ->get() : collect();

        $purchases_in_transaction = $tab === 'transaction' ? $user->purchases()
        ->whereHas('item', function ($query) {
            $query->where('in_transaction', 1); // 購入した商品が取引中か確認
        })
        ->with('item')
        ->get() : collect();

        $purchases_in_transaction_items = $purchases_in_transaction->map(function ($purchase) {
        return $purchase->item;
    });

        $all_transactions = $transactions->merge($purchases_in_transaction_items);

        return view('profile', compact('purchases', 'items', 'all_transactions', 'tab'));
    }

    /**
     * ユーザーのプロフィール編集フォームを表示
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();

        return view('edit', compact('user'));
    }

    /**
     * ユーザーのプロフィールを更新
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // 画像がアップロードされている場合
        if ($request->hasFile('profile_image')) {
            // 古い画像を削除（もし存在すれば）
            if ($user->profile_image) {
                Storage::delete('public/profile_images/'.$user->profile_image);
            }

            // 新しい画像を保存
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');

            // プロフィール画像のパスを保存
            $user->profile_image = basename($imagePath);
        }

        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->address_line = $request->address_line;
        $user->building = $request->building;

        $user->save();

        return redirect()->route('profile.show');
    }
}
