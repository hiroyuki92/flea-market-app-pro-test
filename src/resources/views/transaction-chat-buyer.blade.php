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
            @foreach ($otherItemsInTransaction as $item)
            <li class="item-link">
                <a href="{{ route('transaction.show.buyer', ['item_id' => $item->id]) }}" class="item-link-text">
                    {{ $item->name }}
                </a>
            </li>
            @endforeach
        </ul>
    </aside>
    <main class="main-content">
        <div class="transaction-header">
            <div class="user-info">
                <img class="profile-picture"src="{{ asset('storage/profile_images/' . $seller->profile_image) }}"  alt="ユーザーのプロフィール写真">
                <h1 class="transaction-title">{{ $seller->name }}さんとの取引画面</h1>
            </div>
            <button class="complete-button" onclick="openPopup()">取引を完了する</button>
            <div id="rating-popup" class="popup" onclick="closePopup(event)">
                <div class="popup-content"  onclick="event.stopPropagation()">
                    <div class="popup-header">
                        <h2>取引が完了しました。</h2>
                    </div>
                    <p class="popup-message">今回の取引相手はどうでしたか？</p>
                    <form action="{{ route('submit.buyer.rating') }}" method="POST">
                        @csrf
                        <input type="hidden" name="purchase_id" value="{{ $transaction->id }}">
                        <input type="hidden" name="rating" value="">
                        <div class="star-rating">
                            <span class="star" data-value="1">&#9733;</span>
                            <span class="star" data-value="2">&#9733;</span>
                            <span class="star" data-value="3">&#9733;</span>
                            <span class="star" data-value="4">&#9733;</span>
                            <span class="star" data-value="5">&#9733;</span>
                        </div>

                        <button class="submit-btn" onclick="submitRating()">送信する</button>
                    </form>
                </div>
            </div>
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
                @foreach ($messages->sortBy('created_at') as $message)
                    @if ($message->sender_id == $user->id)
                        <div class="message-right">
                            <div class="message-sender">
                                <div class="user-name">{{ $message->sender->name }}</div>
                                <img class="seller-picture" src="{{ asset('storage/profile_images/' . $message->sender->profile_image) }}" alt="ユーザーのプロフィール写真">
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
                        </div>
                    @else
                        <div class="message-left">
                            <div class="message-sender">
                                <div class="user-name">{{ $message->sender->name }}</div>
                                <img class="seller-picture" src="{{ asset('storage/profile_images/' . $message->sender->profile_image) }}" alt="ユーザーのプロフィール写真">
                            </div>
                            <div class="message-box-left">
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
                        </div>
                    @endif
                @endforeach
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
            </div>
            <div class="message-input-container">
                @error('message')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                @error('image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <form class="message-input-text" method="POST" action="{{ route('transaction.buyerSendMessage', ['item_id' => $transaction->item->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $transaction->item->id }}">
                    <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                    <input type="hidden" name="buyer_id" value="{{ Auth::id() }}">
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

            // ポップアップを表示する関数
            function openPopup() {
                document.getElementById("rating-popup").style.display = "flex";
            }

            // ポップアップを閉じる関数
            function closePopup(event) {
                // 背景部分をクリックしたときにポップアップを閉じる
                if (event.target === document.getElementById("rating-popup")) {
                    document.getElementById("rating-popup").style.display = "none";
                }
            }

            // 評価星を選択できるようにする
            const stars = document.querySelectorAll('.star');
            let selectedRating = null;

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    selectedRating = value;

                    // すべての星の選択をリセット
                    stars.forEach(star => star.classList.remove('selected'));

                    // 選択した星にクラスを追加
                    for (let i = 0; i < value; i++) {
                        stars[i].classList.add('selected');
                    }
                });
            });

            // フォーム送信前に評価が選ばれているかチェック
            document.querySelector('form').addEventListener('submit', function(event) {
                if (!selectedRating) {
                    event.preventDefault();
                    alert("評価を選択してください");
                } else {
                    document.querySelector('input[name="rating"]').value = selectedRating;
                }
            });

            // 評価を送信する関数
            function submitRating() {
                const selectedStar = document.querySelector('.star.selected');
                if (selectedStar) {
                    const rating = selectedStar.getAttribute('data-value');
                    console.log("送信された評価: " + rating);

                    closePopup();
                } else {
                    alert("評価を選択してください");
                }
            }
        </script>
    </main>
@endsection