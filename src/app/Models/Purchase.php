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
     * 取引中のアイテムのみ
     */
    public function scopeInTransaction($query)
    {
        return $query->whereHas('item', function ($itemQuery) {
            $itemQuery->where('in_transaction', 1);
        });
    }

    /**
     * 購入者の視点で取引完了したものを除外
     */
    public function scopeExcludeCompletedForBuyer($query)
    {
        return $query->inTransaction()
                    ->whereHas('item', function ($itemQuery) {
                        $itemQuery->where('completed', false);
                    });
    }

    /**
     * 出品者の視点で取引完了したものを除外
     */
    public function scopeExcludeCompletedForSeller($query)
    {
        return $query->inTransaction()
        ->whereHas('item', function ($itemQuery) {
            $itemQuery->where(function ($conditionQuery) {
                $conditionQuery->where('completed', false)
                ->orWhere(function ($ratingQuery) {
                    $ratingQuery->where('completed', true)
                            ->whereNull('seller_rating');
                });
            });
        });
    }

    // buyer と seller の評価を合算して平均を計算
    public function scopeAverageOverallRating($query, $userId)
    {
        $sellerQuery = clone $query;
        $buyerQuery = clone $query;

        // 売り手として受けた評価の合計と件数
        $sellerStats = $sellerQuery->where('user_id', $userId)
            ->selectRaw('SUM(seller_rating) as sum, COUNT(seller_rating) as count')
            ->whereNotNull('seller_rating')
            ->first();

        $buyerStats = $buyerQuery->whereHas('item', function ($subQuery) use ($userId) {
            $subQuery->where('user_id', $userId);
        })
        ->selectRaw('SUM(buyer_rating) as sum, COUNT(buyer_rating) as count')
        ->whereNotNull('buyer_rating')
        ->first();

        $totalSum = ($sellerStats->sum ?: 0) + ($buyerStats->sum ?: 0);
        $totalCount = ($sellerStats->count ?: 0) + ($buyerStats->count ?: 0);

        return $totalCount > 0 ? round($totalSum / $totalCount) : 0;
    }
}

