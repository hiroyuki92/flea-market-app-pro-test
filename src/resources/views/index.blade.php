@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('link')
<form method="GET" action="{{ route('index') }}" class="search-form">
    <input type="text" class="search-input" name="keyword" value="{{ $keyword  }}" placeholder="なにをお探しですか？" onkeydown="if(event.key === 'Enter'){this.form.submit();}">
</form>
<div class="header-links-group">
    <div class="header-links">
    @if (Auth::check())
        <!-- ログインしている場合はログアウトボタン -->
        <a class="header__link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            ログアウト
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @else
        <!-- ログインしていない場合はログインボタン -->
        <a class="header__link" href="{{ route('login') }}">ログイン</a>
    @endif
    </div>
    <div class="header-links">
    @if (Auth::check())
        <!-- ログインしている場合はマイページにリダイレクト -->
        <a class="header__link" href="{{ route('profile.show') }}">マイページ</a>
    @else
        <!-- ログインしていない場合はログインページにリダイレクト -->
        <a class="header__link" href="{{ route('login') }}">マイページ</a>
    @endif
    </div>
    <div class="header-links">
    @if (Auth::check())
        <!-- ログインしている場合は商品出品ページにリダイレクト -->
        <a class="header__link-create" href="{{ route('create') }}">出品</a>
    @else
        <!-- ログインしていない場合はログインページにリダイレクト -->
        <a class="header__link-create" href="{{ route('login') }}">出品</a>
    @endif
    </div>
</div>
@endsection

@section('content')
<div class="item-list">
    <div class="item-list__heading">
        <a href="{{ url('/?tab=recommended&keyword=' . request('keyword')) }}" class="tab {{ request()->query('tab', 'recommended') === 'recommended' ? 'active' : '' }}">
        おすすめ
        </a>
        <a href="{{ url('/?tab=mylist&keyword=' . request('keyword')) }}" class="tab {{ request()->query('tab') === 'mylist' ? 'active' : '' }}">
        マイリスト
        </a>
    </div>
    <div class="item-grid">
        <!-- おすすめ商品の表示 -->
        @foreach ($items as $item)
        <div class="item-card recommended" style="{{ request()->query('tab') === 'mylist' ? 'display:none;' : '' }}">
            <div class="item-image">
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                    <img class="item-image-picture" src="{{ asset('storage/item_images/' . $item->image_url) }}" alt="{{ $item->name }}">
                </a>
            </div>
            <div class="item-name">
                {{ $item->name }}
            </div>
            @if($item->sold_out)
            <div class="sold-out-label">Sold</div>
            @endif
        </div>
        @endforeach
        <!--マイリスト商品（いいねした商品） -->
        @foreach ($myListItems as $item)
        <div class="item-card mylist" style="{{ request()->query('tab') !== 'mylist' ? 'display:none;' : '' }}">
            <div class="item-image">
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                    <img class="item-image-picture" src="{{ asset('storage/item_images/' . $item->image_url) }}" alt="{{ $item->name }}">
                </a>
            </div>
            <div class="item-name">
                {{ $item->name }}
            </div>
            @if($item->sold_out)
            <div class="sold-out-label">Sold</div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<script>
    function showProducts(type, event) {
        event.preventDefault(); // デフォルトのリンク遷移を防ぐ
        
        const recommendedItems = document.querySelectorAll('.item-card.recommended');
        const mylistItems = document.querySelectorAll('.item-card.mylist');
        const tabs = document.querySelectorAll('.tab');

        // URLを更新
        const newUrl = `/?tab=${type}`;
        window.history.pushState({}, '', newUrl);

        // タブのアクティブ状態を更新
        tabs.forEach(tab => {
            tab.classList.remove('active');
        });
        event.target.classList.add('active');

        // 商品の表示/非表示を切り替え
        recommendedItems.forEach(item => {
            item.style.display = type === 'recommended' ? 'block' : 'none';
        });
        mylistItems.forEach(item => {
            item.style.display = type === 'mylist' ? 'block' : 'none';
        });
    }
</script>

<script>
    document.querySelector('input[name="keyword"]').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            document.getElementById('searchForm').submit();
        }
    });
</script>

@endsection