<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'brand',
        'price',
        'description',
        'image_url',
        'condition',
        'sold_out',
        'in_transaction',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function favoritesCount()
    {
        return $this->favorites()->count();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchases()
    {
        return $this->hasOne(Purchase::class);
    }

    public function scopeKeywordSearch($query, $keyword)
    {
        if (! empty($keyword)) {
            $query->where('name', 'like', '%'.$keyword.'%')
                ->orWhere('description', 'like', '%'.$keyword.'%');
        }
    }

    const STATUS_UNSOLD = 0;
    const STATUS_IN_TRANSACTION = 1;
    const STATUS_SOLD = 2;

    public function getStatusTextAttribute()
    {
        switch ($this->in_transaction) {
            case self::STATUS_UNSOLD:
                return '未取引';
            case self::STATUS_IN_TRANSACTION:
                return '取引中';
            case self::STATUS_SOLD:
                return '取引完了';
            default:
                return '不明';
        }
    }
}
