<?php

namespace App\Http\Controllers;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use App\Models\Chat;
use App\Models\Message;
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
            return $item->id !== $transaction->item->id;
        });

        $chat = Chat::where('item_id', $itemId)
            ->where(function ($query) use ($buyer, $user) {
                $query->where('buyer_id', $buyer->id)
                    ->where('seller_id', $user->id);
            })
            ->first();

        // チャットが存在しない場合は新しく作成
        if (!$chat) {
            $chat = Chat::create([
                'item_id' => $itemId,
                'buyer_id' => $buyer->id,
                'seller_id' => $user->id,
            ]);
        }

        $messages = Message::where('chat_id', $chat->id)->get();

        return view('transaction-chat-seller', compact('transaction', 'buyer', 'itemsInTransaction', 'chat', 'messages'));
    }

    public function sellerSendMessage(Request $request, $itemId)
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

        $chat = Chat::where('item_id', $itemId)
        ->where(function ($query) use ($user, $buyer) {
            $query->where('buyer_id', $buyer->id)
                ->where('seller_id', $user->id);
        })
        ->first();

        if (!$chat) {
        $chat = Chat::create([
            'item_id' => $itemId,
            'buyer_id' => $buyer->id,
            'seller_id' => $user->id,
        ]);
    }

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $request->input('message'),
        ]);

        return redirect()->route('transaction.show', ['item_id' => $itemId]);
    }

    public function update(Request $request)
    {
        $messageModel = Message::find($request->message_id);
        if (!$messageModel) {
            // メッセージが見つからない場合、エラーメッセージを返す
            return redirect()->back()->withErrors('Message not found');
        }

        $messageModel->update(['message' => $request->message]);

        $chat = $messageModel->chat;
        $itemId = $chat ? $chat->item_id : null;

        // item_id が取得できなかった場合のエラーハンドリング
        if (!$itemId) {
            return redirect()->back()->withErrors('Item ID not found');
        }

        return redirect()->route('transaction.show', ['item_id' => $itemId]);
    }

    public function destroy(Request $request)
    {
        $messageModel = Message::find($request->message_id);
        if (!$messageModel) {
            // メッセージが見つからない場合、エラーメッセージを返す
            return redirect()->back()->withErrors('Message not found');
        }

        $messageModel->delete();

        $chat = $messageModel->chat;
        $itemId = $chat ? $chat->item_id : null;

        // item_id が取得できなかった場合のエラーハンドリング
        if (!$itemId) {
            return redirect()->back()->withErrors('Item ID not found');
        }

        return redirect()->route('transaction.show', ['item_id' => $itemId]);
    }
}
