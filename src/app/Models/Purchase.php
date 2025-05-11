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

    // buyer と seller の評価を合算して平均を計算
    public function scopeAverageOverallRating($query, $userId)
    {
        $sellerQuery = clone $query;
        $buyerQuery = clone $query;

        // buyer と seller の評価を取得
        $sellerRating = $sellerQuery->where('user_id', $userId)->avg('seller_rating');//自分が購入者の時の出品者からの評価
        $buyerRating = $buyerQuery->whereHas('item', function ($subQuery) use ($userId) {
            $subQuery->where('user_id', $userId);
        })->avg('buyer_rating');//自分が出品者の時の購入者からの評価

        // 合算して平均を計算
        $totalRatings = 0;
        $totalCount = 0;

        if (!is_null($buyerRating)) {
            $totalRatings += $buyerRating;
            $totalCount++;
        }

        if (!is_null($sellerRating)) {
            $totalRatings += $sellerRating;
            $totalCount++;
        }

        return $totalCount > 0 ? round($totalRatings / $totalCount) : 0; // 平均を四捨五入、評価がなければ0を返す
    }
}

