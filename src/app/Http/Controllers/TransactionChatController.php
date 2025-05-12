<?php

namespace App\Http\Controllers;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use App\Models\Chat;
use App\Models\Message;
use App\Http\Requests\TransactionRequest;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class TransactionChatController extends Controller
{
    // 出品者のチャット画面
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

        $itemsInTransaction = $itemsInTransaction->filter(function ($item) use ($transaction) {
            $isCompletedForSeller = Purchase::where('item_id', $item->id)
                ->excludeCompletedForSeller()  // 出品者目線で取引完了したアイテムを除外
                ->exists();
            return $isCompletedForSeller && $item->id !== $transaction->item->id;// 現在表示している商品を除外
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

        $messages->each(function ($message) {
            if (!$message->is_read) {
                $message->markAsRead();
            }
        });

        $showPopup = $transaction->completed && !$transaction->seller_rating;

        return view('transaction-chat-seller', compact('transaction', 'buyer', 'itemsInTransaction', 'chat', 'messages', 'user', 'showPopup'));
    }

    public function sellerSendMessage(TransactionRequest $request, $itemId)
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

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('image_url', 'public');
            $imageName = basename($imagePath);
        } else {
            $imageName = null;
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $request->input('message'),
            'image_url' => $imageName,
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

        $user = Auth::user();
        $item = Item::find($itemId);

        if ($item && $item->user_id == $user->id) {
            // ユーザーが出品者の場合（出品者用ページにリダイレクト）
            return redirect()->route('transaction.show', ['item_id' => $itemId]);
        } else {
            // ユーザーが購入者の場合（購入者用ページにリダイレクト）
            return redirect()->route('transaction.show.buyer', ['item_id' => $itemId]);
        }
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

        $user = Auth::user();
        $item = Item::find($itemId);

        if ($item && $item->user_id == $user->id) {
            // ユーザーが出品者の場合（出品者用ページにリダイレクト）
            return redirect()->route('transaction.show', ['item_id' => $itemId]);
        } else {
            // ユーザーが購入者の場合（購入者用ページにリダイレクト）
            return redirect()->route('transaction.show.buyer', ['item_id' => $itemId]);
        }
    }

    // 購入者のチャット画面
    public function show(Request $request, $itemId)
    {
        $user = Auth::user();
        $transaction = Purchase::where('item_id', $itemId)
        ->where('user_id', $user->id)
        ->with('item')
        ->first();
        
        if (!$transaction) {
            abort(404); // 取引が存在しないか、現在のユーザーが購入者でない場合
        }
        
        $seller = User::find($transaction->item->user_id);
        if (!$seller) {
            abort(404); // 出品者が見つからない場合
        }

        $purchasesInTransaction = Purchase::where('user_id', $user->id)
            ->excludeCompletedForBuyer() // PurchaseModelのscopeを使用して未完了商品を抽出
            ->with('item')
            ->get();

        $otherItemsInTransaction = $purchasesInTransaction->filter(function ($purchase) use ($transaction) {
            return $purchase->item_id !== $transaction->item_id;
        })->map(function ($purchase) {
            return $purchase->item;
        });

        $chat = Chat::where('item_id', $itemId)
        ->where(function ($query) use ($user, $seller) {
            $query->where('buyer_id', $user->id)
                ->where('seller_id', $seller->id);
        })
        ->first();

        if (!$chat) {
            $chat = Chat::create([
                'item_id' => $itemId,
                'buyer_id' => $user->id,
                'seller_id' => $seller->id,
            ]);
        }

        $messages = Message::where('chat_id', $chat->id)->get();

        return view('transaction-chat-buyer', compact('transaction', 'seller', 'otherItemsInTransaction', 'chat', 'messages', 'user'));
    }

    public function buyerSendMessage(TransactionRequest $request, $itemId)
    {
        $user = Auth::user();
        $transaction = Purchase::where('item_id', $itemId)
        ->where('user_id', $user->id)
        ->with('item')
        ->first();
        if (!$transaction) {
            abort(404); // 取引が存在しない場合
        }
        $seller = User::find($transaction->item->user_id);
        if (!$seller) {
            abort(404); // 出品者が見つからない場合
        }

        $chat = Chat::where('item_id', $itemId)
        ->where(function ($query) use ($user, $seller) {
            $query->where('buyer_id', $user->id)
                ->where('seller_id', $seller->id);
        })
        ->first();

        if (!$chat) {
            $chat = Chat::create([
                'item_id' => $itemId,
                'buyer_id' => $user->id,
                'seller_id' => $seller->id,
            ]);
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('image_url', 'public');
            $imageName = basename($imagePath);
        } else {
            $imageName = null;
        }

        Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $request->input('message'),
            'image_url' => $imageName,
        ]);

        return redirect()->route('transaction.show.buyer', ['item_id' => $itemId]);
    }
}
