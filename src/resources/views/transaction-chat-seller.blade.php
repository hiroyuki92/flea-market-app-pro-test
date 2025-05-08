@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/transaction.css')}}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection

@section('content')
<div class="transaction-container">
    <aside class="sidebar">
        <div class="sidebar-item">その他の取引</div>
        <ul class="item-list">
        @foreach ($itemsInTransaction as $item)
            <li class="item-link">
                <a href="{{ route('transaction.show', ['item_id' => $item->id]) }}" class="item-link-text">
                    {{ $item->name }}
                </a>
            </li>
        @endforeach
        </ul>
    </aside>
    <main class="main-content">
        <div class="transaction-header">
            <div class="user-info">
                <img class="profile-picture"src="{{ asset('storage/profile_images/' . $buyer->profile_image) }}"  alt="ユーザーのプロフィール写真">
                <h1 class="transaction-title">{{ $buyer->name }}さんとの取引画面</h1>
            </div>
            <button class="complete-button">取引を完了する</button>
        </div>

        <div class="item-section">
            <div class="item-image">
                <img class="item-image-picture" src="{{ asset('storage/item_images/' . $transaction->item->image_url) }}" alt="{{ $transaction->item->name }}">
            </div>
            <div class="item-details">
                <h2 class="item-name">{{ $transaction->item->name }}</h2>
                <p class="item-price">¥{{ number_format($transaction->item->price) }}(税込)</p>
            </div>
        </div>

        <div class="chat-section">
            <div class="chat-section-content">
                <div class="message-row">
                    <div class="avatar small"></div>
                    <div class="user-name">ユーザー名</div>
                </div>
                <div class="message-box">
                    <!-- メッセージ内容がここに表示される -->
                </div>
                @foreach ($messages as $message)
                    <div class="message-right">
                        <div class="message-sender">
                            <div class="user-name">{{ $buyer->name }}</div>
                            <img class="seller-picture"src="{{ asset('storage/profile_images/' . $message->sender->profile_image) }}"  alt="ユーザーのプロフィール写真">
                        </div>
                        <div class="message-box-right">
                            {{ $message->message }}
                        </div>
                    </div>
                @endforeach
                <div class="message-actions">
                    <span class="action-link">編集</span>
                    <span class="action-link">削除</span>
                </div>
            </div>
            <div class="message-input-container">
                <form class="message-input-text" method="POST" action="{{ route('transaction.sellerSendMessage', ['item_id' => $transaction->item->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $transaction->item->id }}">
                    <input type="hidden" name="buyer_id" value="{{ $buyer->id }}">
                    <input type="hidden" name="seller_id" value="{{ Auth::id() }}">
                    <textarea class="message-input"  name="message" placeholder="取引メッセージを記入してください">{{ old('message') }}</textarea>
                    <div class="input-actions">
                        <button class="image-button">画像を追加</button>
                        <button class="btn" style="background-color: white;">
                            <i class="bi bi-send" style="color: gray; font-size: 32px;"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
@endsection('content')