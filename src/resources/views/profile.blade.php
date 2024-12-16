@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css')}}">
@endsection

@section('link')
<form method="GET" action="{{ route('index') }}" class="search-form">
    <input type="text" class="search-input" name="keyword" value="{{ old('keyword', '') }}" placeholder="なにをお探しですか？" onkeydown="if(event.key === 'Enter'){this.form.submit();}">
</form>
<div class="header-links-group">
    <div class="header-links">
        <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"   class="header__link">ログアウト</button>
        </form>
    </div>
    <div class="header-links">
        <a class="header__link-mypage" href="{{ route('profile.show') }}">マイページ</a>
    </div>
    <div class="header-links">
        <a class="header__link-create" href="{{ route('create') }}">出品</a>
    </div>
</div>
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <img class="profile-picture"src="{{ asset('storage/profile_images/' . Auth::user()->profile_image) }}"  alt="ユーザーのプロフィール写真">
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
        @foreach ($items as $item)
        <div class="item-card listed">
            <div class="item-image">
                <img class="item-image-picture" src="{{ asset('storage/item_images/' . $item->image_url) }}" alt="{{ $item->name }}">
            </div>
            <div class="item-name">
                {{ $item->name }}
            </div>
            @if($item->sold_out)
            <div class="sold-out-label">Sold Out</div>
            @endif
        </div>
        @endforeach
        <!-- 購入した商品 -->
        @foreach ($purchases as $purchase)
            <div class="item-card purchased" style="display: none;">
                <div class="item-image">
                    <img class="item-image-picture"  src="{{ asset('storage/' . $purchase->item->image_url) }}" alt="{{ $purchase->item->name }}">
                </div>
                <div class="item-name">{{ $purchase->item->name }}</div>
            </div>
        @endforeach
        
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