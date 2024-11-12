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
        <img src="path/to/product-image.jpg" alt="商品画像" />
    </div>
    <div class="item-show-form__info">
        <h2>商品名</h2>
        <p class="item-show-form__brand">ブランド名</p>
        <p class="item-show-form__price">¥47,000 (税込)</p>
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
        <h3>商品説明</h3>
        <div class="item-show-form__description">新品。商品の状態は良好です。</div>
        <h3>商品の情報</h3>
        <div class="item-show-form__content-detail">
            <div class="content__group">
                <div class="content__title">カテゴリー</div>
                <input class="content__text-category" >
            </div>
            <div class="content__group">
                <div class="content__title">商品の状態</div>
                <input class="content__text" >
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