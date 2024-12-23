@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css')}}">
@endsection

@section('content')
<div class="register-form__content">
    <div class="register-form__heading">
        <h2>会員登録</h2>
    </div>
    <form class="form" action="/register" method="post"  novalidate>
        @csrf
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input class="form__input" type="text" name="name" value="{{ old('name') }}" />
                </div>
                <div class="form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">メールアドレス</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input class="form__input" type="email" name="email" value="{{ old('email') }}" />
                </div>
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <!-- パスワード -->
    <div class="form__group">
        <div class="form__group-title">
            <span class="form__label--item">パスワード</span>
        </div>
        <div class="form__group-content">
            <input class="form__input" type="password" name="password" />
            @error('password')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form__group">
        <div class="form__group-title">
            <span class="form__label--item">確認用パスワード</span>
        </div>
        <div class="form__group-content">
            <input class="form__input" type="password" name="password_confirmation" />
            @error('password_confirmation')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>
    </div>
        <div class="form__group--btn">
            <button type="submit" class="register-btn">登録する</button>
        </div>
    </form>
    <div class="form__group--login">
        <a href="/login" class="login-btn">ログインはこちら</a>
    </div>
</div>

@endsection('content')