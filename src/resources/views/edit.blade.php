@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css')}}">
@endsection

@section('link')
<input type="text" class="search-input" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="header__link">ログアウト</button>
</form>
<a class="header__link" href="/register">マイページ</a>
<a class="header__link-create" href="/register">出品</a>
@endsection

@section('content')
<div class="profile-settings-container">
    <h2>プロフィール設定</h2>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="profile-image-container">
            <!-- プロフィール画像の表示 -->
            <img id="profile-image-preview" src="{{ asset('storage/profile_images/' . Auth::user()->profile_image) }}" alt="" class="profile-picture" />
            
            <!-- 非表示のファイル入力 -->
            <input type="file" name="profile_image" id="profile-image" accept="image/*" class="picture-input" onchange="previewImage(event)" style="display: none;" />
            
            <!-- 画像選択ボタン（カスタム） -->
            <button type="button" class="picture-select-btn" onclick="document.getElementById('profile-image').click();">画像を選択する</button>
            @error('profile_image')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input class="form__input" type="text" name="name" value="{{ old('name') }}" />
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
    <script>
    // プロフィール画像のプレビューを表示
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-image-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
    </script>
</div>

@endsection('content')