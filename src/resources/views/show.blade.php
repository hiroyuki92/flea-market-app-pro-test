@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css')}}">
<link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet">
@endsection

@section('link')
<form method="GET" action="{{ route('index') }}" class="search-form">
    <input type="text" class="search-input" name="keyword" value="{{ old('keyword', '') }}" placeholder="なにをお探しですか？" onkeydown="if(event.key === 'Enter'){this.form.submit();}">
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
<div class="item-show-form__content">
    <div class="item-show-form__image">
        <img class="image-picture" src="{{ asset('storage/item_images/' . $item->image_url) }}" alt="商品画像" />
    </div>
    <div class="item-show-form__info">
        <div class="item-name">
            {{ $item->name }}
        </div>
        <div class="item-brand">
            {{ $item->brand }}
        </div>
        <div class="item-price">
            ¥{{ number_format($item->price) }} (税込)
        </div>
        <div class="ratings">
            <div class="stars">
                <form class="form-stars" action="{{ route('item.toggleLike', $item->id) }}" method="POST">
                @csrf
                <button type="submit" class="favorite-button" data-item-id="{{ $item->id }}">
                    @if ($item->favorites()->where('user_id', Auth::id())->exists())
                        <i class="fas fa-star liked"></i> <!-- いいねしている場合 -->
                    @else
                        <i class="far fa-star"></i> <!-- いいねしていない場合 -->
                    @endif
                </button>
                <span class="favorites-count">{{ $item->favorites()->count() }}</span>
            </form>
            </div>
            <div class="comments">
                <i class="far fa-comment"></i>
                <span class="comment-count">{{ $commentsCount }}</span>
            </div>
        </div>
        <div class="purchase-button-group">
            @if ($item->sold_out)
            <div class="sold-out">Sold</div>
            @else
            <a href="{{ url('/purchase/' . $item->id) }}" class="purchase-button">購入手続きへ</a>
            @endif
        </div>
        <div class="item-show-form__title">
            商品説明
        </div>
        <div class="item-description">
            {{ $item->description }}
        </div>
        <div class="item-show-form__title">
            商品の情報
        </div>
        <div class="item-show-form__content-detail">
            <div class="content__group">
                <div class="content__title">カテゴリー</div>
                <div class="category__choices">
                    @foreach($item->categories as $category)
                    <span  class="category-name">{{ $category->name }}</span>
                    @endforeach
                </div>
            </div>
            <div class="content__group">
                <div class="content__title-condition">商品の状態</div>
                    @if ($item->condition == 1)
                    良好
                    @elseif ($item->condition == 2)
                    目立った傷や汚れなし
                    @elseif ($item->condition == 3)
                    やや傷や汚れあり
                    @elseif ($item->condition == 4)
                    状態が悪い
                    @else
                    不明
                    @endif
            </div>
            <div class="content__group-comment-list">
                <div class="comment-list">
                    <div class="comment-list__title">コメント</div>
                    <div class="comment-list__count">({{ $commentsCount }})</div>
                </div>
                @foreach($item->comments as $comment)
                    <div class="comment">
                        <div class ="comment-user">
                            <img class="profile-image" src="{{ asset('storage/profile_images/' . $comment->user->profile_image) }}" alt="{{ $comment->user->name }}" >
                            {{ $comment->user->name }}
                        </div>
                        <div class="comment-content">
                            {{ $comment->content }}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="content__group-comment">
                <div class="content__title-comment">商品へのコメント</div>
                <form action="{{ route('comment.store', $item->id) }}" method="POST">
                    @csrf
                    <textarea class="content-comment" name="comment">{{ old('comment') }}</textarea>
                    @error('comment')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="submit-button">コメントを送信する</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 複数のいいねボタンに対応できるようにする
    document.querySelectorAll('.favorite-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            // data-item-id 属性から item_id を取得
            const itemId = this.getAttribute('data-item-id');

            // form-stars クラスの中から favoritesCountElement を探す
            const favoritesCountElement = this.closest('.form-stars').querySelector('.favorites-count');
            
            if (!favoritesCountElement) {
                console.error('Favorites count element not found');
                return;
            }

            // 動的にURLを生成
            const url = "{{ route('item.toggleLike', ':item_id') }}".replace(':item_id', itemId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }

                const currentCount = parseInt(favoritesCountElement.textContent);
                // 現在の数値が0未満にならないよう保証
                const newCount = data.favorited 
                    ? Math.max(0, currentCount + 1)
                    : Math.max(0, currentCount - 1);
                
                favoritesCountElement.textContent = newCount;

                // アイコンの切り替え
                const icon = this.querySelector('i');
                if (data.favorited) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'liked');
                } else {
                    icon.classList.remove('fas', 'liked');
                    icon.classList.add('far');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
</script>
<script>
    document.querySelector('input[name="keyword"]').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
            event.preventDefault(); // ページリロードを防ぐ
            document.getElementById('searchForm').submit();  // フォーム送信
        }
    });
</script>

@endsection('content')