@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css')}}">
@endsection

@section('link')
<form method="GET" action="{{ route('index') }}" class="search-form">
    <input type="text" class="search-input" name="keyword" value="{{ old('keyword', '') }}" placeholder="なにをお探しですか？" onkeydown="if(event.key === 'Enter'){this.form.submit();}">
</form>
<div class="header-links-group">
    <div class="header-links">
        <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="header__link">ログアウト</button>
        </form>
    </div>
    <div class="header-links">
        <a class="header__link" href="{{ route('profile.show') }}">マイページ</a>
    </div>
    <div class="header-links">
        <a class="header__link-create" href="{{ route('create') }}">出品</a>
    </div>
</div>
@endsection

@section('content')
<div class="profile-settings-container">
    <h2>プロフィール設定</h2>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="profile-image-container">
            <!-- プロフィール画像の表示 -->
            <img id="profile-image-preview"
                src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('storage/profile_images/default.png') }}"
                alt="" class="profile-picture" />
            
            <!-- 非表示のファイル入力 -->
            <input type="file" name="profile_image" id="profile-image" accept="image/*" class="picture-input" onchange="previewImage(event)" style="display: none;" />
            
            <!-- 画像選択ボタン（カスタム） -->
            <button type="button" class="picture-select-btn" onclick="document.getElementById('profile-image').click();">画像を選択する</button>
            
            @error('profile_image')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <!-- ユーザー名 -->
        <div class="form-group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <input class="form__input" type="text" name="name" value="{{ old('name', $user->name) }}" />
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 郵便番号 -->
        <div class="form-group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__group-content">
                <input class="form__input" type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" />
                @error('postal_code')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 住所 -->
        <div class="form-group">
            <div class="form__group-title">
                <span class="form__label--item">住所</span>
            </div>
            <div class="form__group-content">
                <input class="form__input" type="text" name="address_line" value="{{ old('address_line', $user->address_line) }}" />
                @error('address_line')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 建物名 -->
        <div class="form-group">
            <div class="form__group-title">
                <span class="form__label--item">建物名</span>
            </div>
            <div class="form__group-content">
                <input class="form__input" type="text" name="building" value="{{ old('building', $user->building) }}" />
                @error('building')
                    <div class="error-message">{{ $message }}</div>
                @enderror
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
    <script>
        document.querySelector('input[name="keyword"]').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // ページリロードを防ぐ
            document.getElementById('searchForm').submit();  // フォーム送信
        }
    });
    </script>
</div>

@endsection('content')