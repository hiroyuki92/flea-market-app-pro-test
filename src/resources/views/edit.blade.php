@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css')}}">
@endsection

@section('link')
<input type="text" class="search-input" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
<a class="header__link" href="/login">ログイン</a>
<a class="header__link" href="/register">マイページ</a>
<a class="header__link-create" href="/register">出品</a>
@endsection

@section('content')
<div class="profile-settings-container">
    <h2>プロフィール設定</h2>
    <div class="profile-image-container">
        <img src="path/to/profile-image.jpg" alt="プロフィール画像" class="profile-picture">
        <button class="picture-select-btn">画像を選択する</button>
    </div>
    <form class="form" action="#" method="POST">
        <div class="form-group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input class="form__input" type="text" name="username" value="{{ old('username') }}" />
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input class="form__input" type="text" name="postal-code" value="{{ old('postal-code') }}" />
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form__group-title">
                <span class="form__label--item">住所</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input class="form__input" type="text" name="address" value="{{ old('address') }}" />
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form__group-title">
                <span class="form__label--item">建物名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input class="form__input" type="text" name="building" value="{{ old('building') }}" />
                </div>
            </div>
        </div>
        <button type="submit" class="update-btn">更新する</button>
    </form>
</div>

@endsection('content')