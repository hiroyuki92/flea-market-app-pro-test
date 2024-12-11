@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/confirm.css')}}">
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
<div class="purchase-container">
    <div class="container-group">
        <div class="item-detail">
            <div class="item-image">
                <img class="image-picture" src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" />
            </div>
            <div class="item-info">
                <div class="item-name">
                {{ $item->name }}
                </div>
                <div class="item-price">
                    ¥ {{ number_format($item->price) }}
                </div>
            </div>
        </div>
        <div class="payment-details">
            <div class="payment-title">支払い方法</div>
            <select name="payment-method"  id="payment-method">>
                <option value=""hidden>選択してください</option>
                <option value="convenience">コンビニ払い</option>
                <option value="credit-card">カード支払い</option>
            </select>
        </div>
        <div class="shipping-details">
            <div class="shipping-group">
                <div class="shipping-title">配送先</div>
                <div class="shipping-change-btn">
                    <a href="{{ route('address.edit', ['item_id' => $item->id]) }}">変更する</a>
                </div>
            </div>
            <p>{{ $shippingAddress['shipping_postal_code'] }}</p>
            <p>{{ $shippingAddress['shipping_address_line'] }}</p>
            <p>{{ $shippingAddress['shipping_building'] }}</p>
        </div>
    </div>
    <div class="summary">
        <table>
            <tr>
                <td>商品代金</td>
                <td>¥ {{ number_format($item->price) }}</td>
            </tr>
            <tr>
                <td>支払い方法</td>
                <td id="payment-method-display"></td>
            </tr>
        </table>
        <button class="purchase-btn">購入する</button>
    </div>
    <script>
        document.querySelector('input[name="keyword"]').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // ページリロードを防ぐ
            document.getElementById('searchForm').submit();  // フォーム送信
        }
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment-method');
        
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function() {
                // 選択されたoptionのvalueではなく、optionのテキストを取得
                var selectedPaymentMethod = this.options[this.selectedIndex].text;
                
                var display = document.getElementById('payment-method-display');
                
                if (selectedPaymentMethod) {
                    display.textContent = selectedPaymentMethod;
                } else {
                    display.textContent = '';  // 選択肢が空の場合は表示しない
                }
            });
        }
    });
</script>
</div>

@endsection('content')