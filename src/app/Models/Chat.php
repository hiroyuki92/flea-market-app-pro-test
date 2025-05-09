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
}
