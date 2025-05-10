<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'shipping_postal_code',
        'shipping_address_line',
        'shipping_building',
        'payment_method'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 購入者の視点で取引完了したものを除外
     */
    public function scopeExcludeCompletedForBuyer($query)
    {
        return $query->whereHas('item', function ($query) {
            $query->where('in_transaction', 1)
                ->where('completed', false);
        });
    }

    /**
     * 出品者の視点で取引完了したものを除外
     */
    public function scopeExcludeCompletedForSeller($query)
    {
        return $query->whereHas('item', function ($query) {
        $query->where('in_transaction', 1)
            ->where(function ($query) {
                $query->where('completed', false)
                    ->orWhere(function ($query) {
                        $query->where('completed', true)
                            ->whereNull('seller_rating');  // seller_ratingがnullの商品
                    });
                });
        });
    }
}

