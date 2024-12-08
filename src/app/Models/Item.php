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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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
        return $this->hasMany(Purchase::class);
    }

    public function scopeKeywordSearch($query, $keyword)
    {
    if (!empty($keyword)) {
    $query->where('name', 'like', '%' . $keyword . '%')
    ->orWhere('description', 'like', '%' . $keyword . '%');
  }
    }
}

