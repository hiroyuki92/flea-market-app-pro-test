@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
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
<div class="item-create-form__content">
    <div class="item-create-form__heading">
        <h2>商品の出品</h2>
    </div>
    <form class="item-create-form" method="POST" action="{{ route('item.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="item-create-form__group">
            <div class="form__label">商品画像</div>
            <div class="form__btn">
                <label for="imageUpload" class="img-input__btn">画像を選択する</label>
                <input type="file" id="imageUpload" class="img-upload" name="image" accept="image/*" onchange="previewImage(event)" />
                <img id="preview" class="preview" src="" alt="選択した画像のプレビュー" style="display: none;" />
            </div>
            @error('image')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="item-create-form__detail">
            <div class="item-create-form__detail-title">
                <h3>商品の詳細</h3>
            </div>
            <div class="item-create-form__group">
                <div class="form__label">カテゴリー</div>
                <div class="category__tag">
                    @foreach($categories as $category)
                    <span class="tag" data-id="{{ $category->id }}" onclick="selectCategory(this)">
                        {{ $category->name }}
                    </span>
                    @endforeach
                </div>
                <input type="hidden" name="category_ids" id="selected-category-ids" value="{{ old('category_ids', '') }}">
                @error('category_ids')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="item-create-form__group">
                <div class="form__label">商品の状態</div>
                <div class="form__select">
                    <select class="form__select-group" name="condition">
                        <option value=""hidden>選択してください</option>
                        <option value="1" {{ old('condition') == '1' ? 'selected' : '' }}>良好</option>
                        <option value="2" {{ old('condition') == '2' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                        <option value="3" {{ old('condition') == '3' ? 'selected' : '' }}>やや傷や汚れあり</option>
                        <option value="4" {{ old('condition') == '4' ? 'selected' : '' }}>状態が悪い</option>
                    </select>
                </div>
                @error('condition')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="item-create-form__name">
            <div class="item-create-form__detail-title">
                <h3>商品名と説明</h3>
            </div>
            <div class="item-create-form__group">
                <div class="form__label">商品名</div>
                <div class="form__input--text">
                    <input class="form__input" type="text" name="name" value="{{ old('name') }}" />
                </div>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="item-create-form__group">
                <div class="form__label">ブランド名</div>
                <div class="form__input--text">
                    <input class="form__input" type="text" name="brand" value="{{ old('brand') }}" />
                </div>
                @error('brand')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="item-create-form__group">
                <div class="form__label">商品の説明</div>
                <div class="form__input--description">
                    <textarea class="form__input--description-content" name="description" >{{ old('description') }}</textarea>
                </div>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="item-create-form__group">
                <div class="form__label">販売価格</div>
                <div class="form__input--price">
                    <span class="currency">¥</span>
                    <input class="form__input--price-content" type="number" name="price" value="{{ old('price') }}" />
                </div>
                @error('price')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
    <script>
        let selectedCategories = [];

    function selectCategory(element) {
    const categoryId = element.getAttribute('data-id');
    
    // 選択済みのカテゴリーIDを配列に追加
    if (!selectedCategories.includes(categoryId)) {
        selectedCategories.push(categoryId);
        element.classList.add('selected');  // 選択されたカテゴリーにクラスを追加
    } else {
        // すでに選択されている場合、選択を解除
        selectedCategories = selectedCategories.filter(id => id !== categoryId);
        element.classList.remove('selected');
    }

    // 選択されたカテゴリーIDを隠しフィールドに設定
    document.getElementById('selected-category-ids').value = selectedCategories.join(',');
}

    // ページが再読み込みされた際に選択されたカテゴリーを反映させる
    window.onload = function() {
        const selectedCategories = document.getElementById('selected-category-ids').value.split(',');
        const tags = document.querySelectorAll('.category__tag .tag');

        // 初期選択状態を反映
        tags.forEach(tag => {
            if (selectedCategories.includes(tag.getAttribute('data-id'))) {
                tag.classList.add('selected');
            }
        });
    };

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
    <script>
        document.querySelector('input[name="keyword"]').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // ページリロードを防ぐ
            document.getElementById('searchForm').submit();  // フォーム送信
        }
    });
    </script>

</div>
@endsection

