<?php

namespace App\Http\Controllers;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class TransactionChatController extends Controller
{
    public function index(Request $request, $itemId)
    {
        $user = Auth::user();
        $transaction = Purchase::where('item_id', $itemId)
        ->with('item')
        ->first();
        if (!$transaction) {
            abort(404); // 取引が存在しない場合
        }
        $buyer = User::find($transaction->user_id);
        if (!$buyer) {
            abort(404); // 購入者が見つからない場合
        }

        $itemsInTransaction = Item::where('user_id', $user->id)
        ->where('in_transaction', 1)
        ->get();

        // 現在表示している商品を除外
        $itemsInTransaction = $itemsInTransaction->filter(function ($item) use ($transaction) {
            return $item->id !== $transaction->item->id; // 現在表示している商品（$transaction->item）は除外
        });

        return view('transaction-chat', compact('transaction', 'buyer', 'itemsInTransaction'));
    }
}
