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
                <img class="profile-picture"src="{{ asset('storage/profile_images/' . Auth::user()->profile_image) }}"  alt="ユーザーのプロフィール写真">
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
            <div class="message-row">
                <div class="avatar small"></div>
                <div class="user-name">ユーザー名</div>
            </div>
            <div class="message-box">
                <!-- メッセージ内容がここに表示される -->
            </div>

            <div class="message-row right">
                <div class="user-name">ユーザー名</div>
                <div class="avatar small"></div>
            </div>
            <div class="message-box right">
                <!-- 返信メッセージがここに表示される -->
            </div>
            <div class="message-actions">
                <span class="action-link">編集</span>
                <span class="action-link">削除</span>
            </div>

            <div class="message-input-container">
                <input type="text" class="message-input" placeholder="取引メッセージを記入してください">
                <div class="input-actions">
                    <button class="image-button">画像を追加</button>
                    <button class="btn" style="background-color: white;">
                        <i class="bi bi-send" style="color: gray; font-size: 32px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection('content')