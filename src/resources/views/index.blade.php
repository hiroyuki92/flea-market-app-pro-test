@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('link')
<input type="text" class="search-input" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
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
<a class="header__link" href="/register">マイページ</a>
<a class="header__link-create" href="/register">出品</a>
@endsection

@section('content')
<div class="item-list">
    <div class="item-list__heading">
        <div class="tab active" onclick="showProducts('recommended')">おすすめ</div>
        <div class="tab" onclick="showProducts('mylist')">マイリスト</div>
    </div>
    <div class="item-grid">
        <!-- おすすめ商品の表示 -->
        <div class="item-card recommended">商品画像<br>商品名1</div>
        <div class="item-card recommended">商品画像<br>商品名2</div>
        <div class="item-card recommended">商品画像<br>商品名3</div>
        <div class="item-card recommended">商品画像<br>商品名4</div>
        <!-- マイリスト商品（いいねした商品） -->
        <div class="item-card mylist" style="display: none;">商品画像<br>いいね商品1</div>
        <div class="item-card mylist" style="display: none;">商品画像<br>いいね商品2</div>
    </div>
    <script>
        function showProducts(type) {
            const recommendedItems = document.querySelectorAll('.item-card.recommended');
            const mylistItems = document.querySelectorAll('.item-card.mylist');
            const tabs = document.querySelectorAll('.tab');

            // タブのアクティブ状態を更新
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            if (type === 'recommended') {
                tabs[0].classList.add('active');
            } else {
                tabs[1].classList.add('active');
            }

            // 商品の表示/非表示を切り替え
            recommendedItems.forEach(item => {
                item.style.display = type === 'recommended' ? 'block' : 'none';
            });
            mylistItems.forEach(item => {
                item.style.display = type === 'mylist' ? 'block' : 'none';
            });
        }
    </script>

</div>

@endsection('content')