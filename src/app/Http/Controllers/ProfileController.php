<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Chat;

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
            ->whereHas('purchases', function ($query) {
                $query->excludeCompletedForSeller();
            })
            ->get() : collect();

        $purchases_in_transaction = $tab === 'transaction' ? $user->purchases()
            ->excludeCompletedForBuyer()
            ->with('item')
            ->get() : collect();

        $purchases_in_transaction_items = $purchases_in_transaction->map(function ($purchase) {
            return $purchase->item;
        });

        $all_transactions = $transactions->merge($purchases_in_transaction_items);

          // メッセージを時間順に並べる
        $itemsWithMessages = $all_transactions->map(function ($item) use ($user) {
            $chat = Chat::where('item_id', $item->id)
                ->where(function ($query) use ($user) {
                    $query->where('buyer_id', $user->id)
                        ->orWhere('seller_id', $user->id);
                })
                ->orderMessagesByTime()  // メッセージを時間順に並べる
                ->first();

            if ($chat) {
                $item->last_message_time = $chat->messages->first()->created_at ?? null;  // 最新のメッセージの時間を取得
            }

            return $item;
        });
        // メッセージの届いた時間順にアイテムを並べる
        $sortedItems = $itemsWithMessages->sortByDesc('last_message_time');

        $unreadInfo = Chat::getUnreadMessagesForUserTransactions($user->id);
        $itemsWithUnreadCount = $unreadInfo['itemsWithUnreadCount'];
        $itemsWithUnreadMessages = $unreadInfo['itemsWithUnreadMessages'];

        return view('profile', compact('purchases', 'items', 'tab', 'itemsWithUnreadCount','itemsWithUnreadMessages', 'sortedItems'));
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
