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
                        @if ($message->image_url)
                            <img class="message-picture" src="{{ asset('storage/image_url/' . $message->image_url) }}" alt="メッセージ画像">
                        @endif
                        <div class="button-group">
                            <button class="update-form__button-submit" onclick="openEditModal({{ $message->id }}, '{{ addslashes($message->message) }}')">編集</button>
                            <form class="delete-form" action="/transaction/delete" method="post">
                                @method('DELETE')
                                @csrf
                                <input type="hidden" name="message_id" value="{{ $message->id }}">
                                <div class="button-group">
                                    <button class="delete-form__button-submit" type="submit">削除</button>
                                </div>
                            </form>
                        </div>
                        <div id="edit-modal" class="modal">
                        <div class="modal-content">
                            <span class="close-button" onclick="closeEditModal()">&times;</span>
                            <div class="modal-title">メッセージを編集</div>
                            
                            <form id="edit-form" action="/transaction/update" method="post">
                                @csrf
                                @method('PATCH')
                                <textarea id="edit-message-text" name="message" class="edit-textarea"></textarea>
                                <input type="hidden" id="edit-message-id" name="message_id" value="">
                                
                                <div class="modal-buttons">
                                    <button type="button" onclick="closeEditModal()">キャンセル</button>
                                    <button type="submit" class="save-button">保存</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="message-input-container">
                @error('message')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                @error('image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <form class="message-input-text" method="POST" action="{{ route('transaction.sellerSendMessage', ['item_id' => $transaction->item->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $transaction->item->id }}">
                    <input type="hidden" name="buyer_id" value="{{ $buyer->id }}">
                    <input type="hidden" name="seller_id" value="{{ Auth::id() }}">
                    <textarea class="message-input"  name="message" placeholder="取引メッセージを記入してください">{{ old('message') }}</textarea>
                    <div class="input-actions">
                        <img id="preview" class="preview" src="" alt="選択した画像のプレビュー" style="display: none;" />
                        <div class="image-button">
                            <label for="imageUpload" class="img-input__btn">画像を追加</label>
                            <input type="file" id="imageUpload" class="img-upload" name="image" accept="image/*" onchange="previewImage(event)" />
                        </div>
                        <button class="btn" style="background-color: transparent;">
                            <i class="bi bi-send" style="color: gray; font-size: 32px; "></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
    // 編集モーダルを開く
    function openEditModal(messageId, messageText) {
        // モーダルを表示
        document.getElementById('edit-modal').style.display = 'block';
        
        // フォームに値をセット
        document.getElementById('edit-message-id').value = messageId;
        document.getElementById('edit-message-text').value = messageText.replace(/\\'/g, "'");
        
        // テキストエリアにフォーカス
        document.getElementById('edit-message-text').focus();
        }
        
        // 編集モーダルを閉じる
        function closeEditModal() {
            document.getElementById('edit-modal').style.display = 'none';
        }
        
        // モーダル外をクリックした時に閉じる
        window.onclick = function(event) {
            var modal = document.getElementById('edit-modal');
            if (event.target == modal) {
                closeEditModal();
            }
        }

        // 画像プレビューの表示
            function previewImage(event) {
            const file = event.target.files[0]; // 最初のファイルを取得
            const previewImageElement = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                // 画像を読み込む
                reader.onload = function(e) {
                    // 読み込んだ画像のデータURLをimgタグにセット
                    previewImageElement.src = e.target.result;
                    // プレビュー部分を表示
                    previewImageElement.style.display = 'block';
                };
                reader.readAsDataURL(file); // 画像をData URLとして読み込む
            } else {
                // 画像が選択されていない場合、プレビューを非表示にする
                previewImageElement.style.display = 'none';
            }
        }
    </script>
</div>
@endsection('content')