<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'buyer_id', 'seller_id'];

    public function chats()
    {
        return $this->hasMany(Message::class);
    }
}
