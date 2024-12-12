<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];  // カテゴリ名をfillableに設定

    // 商品とのリレーション
    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_item');
    }
}
