@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css')}}">
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
<div class="address-change-form">
    <h2>住所の変更</h2>
    <form class="address-change-form-content" action="{{ route('address.update', ['item_id' => $item->id]) }}"  method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input class="postal_code-input" type="text" name="postal_code">
            @error('postal_code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="address">住所</label>
            <input class="address-input" type="text" name="address_line">
            @error('address_line')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="building">建物名</label>
            <input class="building-input" type="text" name="building">
            @error('building')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="update-btn">更新する</button>
    </form>
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