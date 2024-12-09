@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css')}}">
@endsection

@section('link')
<input type="text" class="search-input" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="header__link">ログアウト</button>
</form>
<a class="header__link" href="{{ route('profile.show') }}">マイページ</a>
<a class="header__link-create" href="{{ route('create') }}">出品</a>
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <img class="profile-picture"src="{{ asset('storage/' . Auth::user()->profile_image) }}"  alt="ユーザーのプロフィール写真">
        <div class="profile-info">{{ Auth::user()->name }}</div>
        <div class="profile-edit">
            <a href="/mypage/profile" class="edit-profile-btn">プロフィールを編集</a>
        </div>
    </div>
    <div class="item-list__heading">
        <div class="tab active" onclick="showProducts('listed')">出品した商品</div>
        <div class="tab" onclick="showProducts('purchased')">購入した商品</div>
    </div>
    <div class="item-grid">
        <!-- 出品した商品 -->
        <div class="item-card listed">
            <div class="item-image">
                <img src="{{ asset('images/product1.jpg') }}" alt="商品画像">
            </div>
            <div class="item-name">商品名</div>
        </div>
        <div class="item-card listed">
            <div class="item-image">
                <img src="{{ asset('images/product2.jpg') }}" alt="商品画像">
            </div>
            <div class="item-name">商品名</div>
        </div>
        <!-- 購入した商品 -->
        <div class="item-card purchased" style="display: none;">
            <div class="item-image">
                <img src="{{ asset('images/product3.jpg') }}" alt="購入商品画像">
            </div>
            <div class="item-name">購入商品名</div>
        </div>
        <div class="item-card purchased" style="display: none;">
            <div class="item-image">
                <img src="{{ asset('images/product4.jpg') }}" alt="購入商品画像">
            </div>
            <div class="item-name">購入商品名</div>
        </div>
    </div>
    <script>
        function showProducts(type) {
            const listedItems = document.querySelectorAll('.item-card.listed');
            const purchasedItems = document.querySelectorAll('.item-card.purchased');
            const tabs = document.querySelectorAll('.tab');

            // タブのアクティブ状態を更新
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            if (type === 'listed') {
                tabs[0].classList.add('active');
            } else {
                tabs[1].classList.add('active');
            }

            // 商品の表示/非表示を切り替え
            listedItems.forEach(item => {
                item.style.display = type === 'listed' ? 'block' : 'none';
            });
            purchasedItems.forEach(item => {
                item.style.display = type === 'purchased' ? 'block' : 'none';
            });
        }
    </script>
</div>

@endsection('content')