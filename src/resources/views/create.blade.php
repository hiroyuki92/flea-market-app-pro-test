@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css')}}">
@endsection

@section('link')
<input type="text" class="search-input" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
<a class="header__link" href="/login">ログイン</a>
<a class="header__link" href="/register">マイページ</a>
<a class="header__link-create" href="/sell">出品</a>
@endsection

@section('content')
<div class="item-create-form__content">
    <div class="item-create-form__heading">
        <h2>商品の出品</h2>
    </div>
        <form class="item-create-form" >
        @csrf
        <div class="item-create-form__group">
            <div class="form__label">商品画像</div>
            <div class="form__btn">
                <label for="imageUpload" class="img-input__btn">画像を選択する</label>
                <input type="file" id="imageUpload" class="img-upload" name="image" accept="image/*" />
                <img id="preview" class="preview" src="" alt="選択した画像のプレビュー" />
            </div>
        </div>
        <div class="item-create-form__detail">
            <div class="item-create-form__detail-title">
                <h3>商品の詳細</h3>
            </div>
            <div class="item-create-form__group">
                <div class="form__label">カテゴリー</div>
                <div class="category__tag">
                    <span class="tag">ファッション</span>
                    <span class="tag">家電</span>
                </div>
            </div>
            <div class="item-create-form__group">
                <div class="form__label">商品の状態</div>
                <div class="form__select">
                    <select class="form__select-group"  name="condition" required>
                        <option value="">選択してください</option>
                        <option  value="1">良好</option>
                        <option value="2">目立った傷や汚れなし</option>
                        <option value="3">やや傷や汚れなし</option>
                        <option value="4">状態が悪い</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="item-create-form__name">
            <div class="item-create-form__detail-title">
                <h3>商品名と説明</h3>
            </div>
            <div class="item-create-form__group">
                <div class="form__label">商品名</div>
                <div class="form__input--text">
                    <input class="form__input" type="text" name="name" value="{{ old('name') }}" required />
                </div>
            </div>
            <div class="item-create-form__group">
                <div class="form__label">ブランド名</div>
                <div class="form__input--text">
                    <input class="form__input" type="text" name="brand" value="{{ old('brand') }}" />
                </div>
            </div>
            <div class="item-create-form__group">
                <div class="form__label">商品の説明</div>
                <div class="form__input--description">
                    <textarea class="form__input--description-content" name="description" required>{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="item-create-form__group">
                <div class="form__label">販売価格</div>
                <div class="form__input--price">
                    <span class="currency">¥</span>
                    <input class="form__input--price-content" type="text" name="price" value="{{ old('price') }}" />
                </div>
            </div>
            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection('content')