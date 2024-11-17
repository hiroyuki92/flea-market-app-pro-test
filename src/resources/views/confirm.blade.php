@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/confirm.css')}}">
@endsection

@section('link')
<input type="text" class="search-input" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="header__link-logout">ログアウト</button>
</form>
<a class="header__link" href="/register">マイページ</a>
<a class="header__link-create" href="/register">出品</a>
@endsection

@section('content')
<div class="purchase-container">
    <div class="container-group">
    <div class="item-detail">
        <div class="item-image">
            <img src="" alt="商品画像">
        </div>
        <div class="item-info">
            <h2>商品名</h2>
            <div class="item-price">¥47,000</div>
        </div>
    </div>
    <div class="payment-details">
        <div class="payment-title">支払い方法</div>
        <select name="payment-method">
            <option value="">選択してください</option>
            <option value="credit">クレジットカード</option>
            <option value="convenience">コンビニ払い</option>
            <option value="bank">銀行振込</option>
        </select>
    </div>
    <div class="shipping-details">
        <div class="shipping-group">
            <div class="shipping-title">配送先</div>
            <div class="shipping-change-btn">
                <a href="/purchase/address">変更する</a>
            </div>
        </div>
        <p>〒 XXX-YYYY</p>
        <p>ここには住所と建物が入ります</p>
    </div>
</div>
    <div class="summary">
        <table>
            <tr>
                <td>商品代金</td>
                <td>¥47,000</td>
            </tr>
            <tr>
                <td>支払い方法</td>
                <td>コンビニ払い</td>
            </tr>
        </table>
        <button class="purchase-btn">購入する</button>
    </div>
</div>

@endsection('content')