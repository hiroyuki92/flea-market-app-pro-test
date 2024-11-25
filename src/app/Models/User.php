<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'postal_code',
        'address_line',
        'building',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // ユーザーが複数の購入を行う
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    // ユーザーが複数の商品を出品する
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // ユーザーが複数の「いいね」を持つ
    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites');
    }

    // ユーザーが複数のコメントを投稿する
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
