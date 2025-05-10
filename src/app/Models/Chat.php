<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'buyer_id', 'seller_id'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function unreadMessagesFor($userId)
    {
        return $this->messages()
                    ->where('sender_id', '!=', $userId)
                    ->where('is_read', false)
                    ->count();
    }

    public function scopeWithUnreadCount($query, $userId)
    {
        return $query->withCount(['messages as unread_count' => function($query) use ($userId) {
            $query->where('sender_id', '!=', $userId)
                ->where('is_read', false);
        }]);
    }

    public static function getUnreadMessagesForUserTransactions($userId)
    {
        // ユーザーを取得
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return [
                'itemsWithUnreadCount' => [],
                'itemsWithUnreadMessages' => 0
            ];
        }

        // 取引中の商品を取得
        $transactions = $user->items()
            ->where('in_transaction', 1)
            ->get();
            
        $purchases_in_transaction = $user->purchases()
            ->whereHas('item', function ($query) {
                $query->where('in_transaction', 1);
            })
            ->with('item')
            ->get();
            
        $purchases_in_transaction_items = $purchases_in_transaction->map(function ($purchase) {
            return $purchase->item;
        });
        
        $all_transactions = $transactions->merge($purchases_in_transaction_items);
        
        // 取引中の商品IDを収集
        $transactionItemIds = $all_transactions->pluck('id');
        
        $itemsWithUnreadCount = [];
        
        if (count($transactionItemIds) > 0) {
            $chats = self::whereIn('item_id', $transactionItemIds)
                ->withUnreadCount($userId)
                ->get();
                
            foreach ($chats as $chat) {
                $itemsWithUnreadCount[$chat->item_id] = ($itemsWithUnreadCount[$chat->item_id] ?? 0) + $chat->unread_count;
            }
        }
        
        // 未読メッセージがある商品数をカウント
        $itemsWithUnreadMessages = count(array_filter($itemsWithUnreadCount, function($count) {
            return $count > 0;
        }));
        
        return [
            'itemsWithUnreadCount' => $itemsWithUnreadCount,
            'itemsWithUnreadMessages' => $itemsWithUnreadMessages
        ];

    }

    /**
     * チャットメッセージを時間順に並べるスコープ
     */
    public function scopeOrderMessagesByTime($query)
    {
        return $query->with(['messages' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);
    }
}
