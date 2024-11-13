@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css')}}">
@endsection

@section('link')
<input type="text" class="search-input" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
<a class="header__link" href="/login">ログイン</a>
<a class="header__link" href="/register">マイページ</a>
<a class="header__link-create" href="/register">出品</a>
@endsection

@section('content')
<div class="address-change-form">
    <h2>住所の変更</h2>
    <form class="address-change-form-content">
        <div class="form-group">
            <label for="postal-code">郵便番号</label>
            <input class="postal-code-input" type="text" name="postal-code">
        </div>
        <div class="form-group">
            <label for="address">住所</label>
            <input class="address-input" type="text" name="address">
        </div>
        <div class="form-group">
            <label for="building">建物名</label>
            <input class="building-input" type="text" name="building">
        </div>
        <button type="submit" class="update-btn">更新する</button>
    </form>
</div>
@endsection('content')