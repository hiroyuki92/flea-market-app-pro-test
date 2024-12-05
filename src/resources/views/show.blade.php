@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css')}}">
<link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet">
@endsection

@section('link')
<input type="text" class="search-input" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
<a class="header__link" href="/login">ログイン</a>
<a class="header__link" href="/register">マイページ</a>
<a class="header__link-create" href="/sell">出品</a>
@endsection

@section('content')
<div class="item-show-form__content">
    <div class="item-show-form__image">
        <img class="image-picture" src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" />
    </div>
    <div class="item-show-form__info">
        <div class="item-name">
            {{ $item->name }}
        </div>
        <div class="item-brand">
            {{ $item->brand }}
        </div>
        <div class="item-price">
            ¥{{ number_format($item->price) }} (税込)
        </div>
        <div class="ratings">
            <div class="stars">
                <i class="far fa-star"></i>
                <span class="comment-count">3</span> <!-- いいね数 -->
            </div>
            <div class="comments">
                <i class="far fa-comment"></i>
                <span class="comment-count">3</span> <!-- コメント数 -->
            </div>
        </div>
        <div class="purchase-button-group">
        <a href="#" class="purchase-button">購入手続きへ</a>
        </div>
        <div class="item-show-form__title">
            商品説明
        </div>
        <div class="item-description">
            {{ $item->description }}
        </div>
        <div class="item-show-form__title">
            商品の情報
        </div>
        <div class="item-show-form__content-detail">
            <div class="content__group">
                <div class="content__title">カテゴリー</div>
                <div class="category__choices">
                    {{ $item->category->name }}
                </div>
            </div>
            <div class="content__group">
                <div class="content__title">商品の状態</div>
                    @if ($item->condition == 1)
                    良好
                    @elseif ($item->condition == 2)
                    目立った傷や汚れなし
                    @elseif ($item->condition == 3)
                    やや傷や汚れあり
                    @elseif ($item->condition == 4)
                    状態が悪い
                    @else
                    不明
                    @endif
            </div>
                <div class="content__group-comment-list">
                <div class="comment-list__title">コメント</div>
            </div>
            <div class="content__group-comment">
                <div class="content__title-comment">商品へのコメント</div>
                <textarea class="content-comment" name="comment" required>{{ old('comment') }}</textarea>
            </div>
            <button type="submit" class="submit-button">コメントを送信する</button>
        </div>
    </div>
</div>

@endsection('content')