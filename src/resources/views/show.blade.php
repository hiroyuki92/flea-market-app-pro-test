@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css')}}">
<link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet">
@endsection

@section('link')
<input type="text" class="search-input" name="keyword" value="{{ old('keyword') }}" placeholder="なにをお探しですか？">
<a class="header__link" href="/login">ログイン</a>
<a class="header__link" href="/register">マイページ</a>
<a class="header__link-create" href="/sell">出品</a>
@endsection

@section('content')
<div class="item-show-form__content">
    <div class="item-show-form__image">
        <img class="image-picture" src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" />
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
                <button type="submit" class="favorite-button">
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
        <a href="#" class="purchase-button">購入手続きへ</a>
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
                    {{ $item->category->name }}
                </div>
            </div>
            <div class="content__group">
                <div class="content__title">商品の状態</div>
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
            </div>
            <button type="submit" class="submit-button">コメントを送信する</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const favoriteButton = document.querySelector('.favorite-button');
    if (favoriteButton) {
        favoriteButton.addEventListener('click', function(event) {
            event.preventDefault();
            const itemId = {{ $item->id }};
            // form-stars クラスの中から favoritesCountElement を探す
            const favoritesCountElement = this.closest('.form-stars').querySelector('.favorites-count');
            
            if (!favoritesCountElement) {
                console.error('Favorites count element not found');
                return;
            }

            fetch(`/items/${itemId}/toggle-like`, {
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
    }
});
</script>

@endsection('content')