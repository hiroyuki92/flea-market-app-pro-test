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
        <div class ="profile-info">
            <div class="profile-info-name">{{ Auth::user()->name }}</div>
            @if ($averageOverallRating > 0)
                <div class="star-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= $averageOverallRating ? 'filled' : 'empty' }}">&#9733;</span>
                    @endfor
                </div>
            @endif
            </div>
        <div class="profile-edit">
            <a href="/mypage/profile" class="edit-profile-btn">プロフィールを編集</a>
        </div>
    </div>
    <div class="item-list__heading">
        <a href="{{ url('mypage?tab=sell') }}" class="tab {{ request('tab') === 'sell' || is_null(request('tab')) ? 'active' : '' }}">出品した商品</a>
        <a href="{{ url('mypage?tab=buy') }}" class="tab {{ request('tab') === 'buy' ? 'active' : '' }}">購入した商品</a>
        <a href="{{ url('mypage?tab=transaction') }}" class="tab {{ request('tab') === 'transaction' ? 'active' : '' }}">取引中の商品
            @if($itemsWithUnreadMessages > 0)
                <span class="badge bg-danger">{{ $itemsWithUnreadMessages }}</span>
            @endif
        </a>
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
            <div class="sold-out-label">Sold</div>
            @endif
        </div>
        @endforeach
        <!-- 購入した商品 -->
        @foreach ($purchases as $purchase)
            <div class="item-card purchased">
                <div class="item-image">
                    <img class="item-image-picture"  src="{{ asset('storage/item_images/' . $purchase->item->image_url) }}" alt="{{ $purchase->item->name }}">
                </div>
                <div class="item-name">{{ $purchase->item->name }}</div>
            </div>
        @endforeach
        <!-- 取引中の商品 -->
        @foreach ($sortedItems as $item)
            <div class="item-card transaction">
                <div class="item-image">
                    @if(isset($itemsWithUnreadCount[$item->id]) && $itemsWithUnreadCount[$item->id] > 0)
                        <div class="unread-badge-overlay">
                            <span class="badge rounded-circle bg-danger">{{ $itemsWithUnreadCount[$item->id] }}</span>
                        </div>
                    @endif
                    @if($item->user_id === Auth::id())
                        <a href="{{ route('transaction.show', ['item_id' => $item->id]) }}">
                            <img class="item-image-picture"  src="{{ asset('storage/item_images/' . $item->image_url) }}" alt="{{ $item->name }}">
                        </a>
                    @else
                        <a href="{{ route('transaction.show.buyer', ['item_id' => $item->id]) }}">
                            <img class="item-image-picture"  src="{{ asset('storage/item_images/' . $item->image_url) }}" alt="{{ $item->name }}">
                        </a>
                    @endif
                </div>
                <div class="item-name">{{ $item->name }}</div>
            </div>
        @endforeach
    </div>
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