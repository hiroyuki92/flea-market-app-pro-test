@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css')}}">
@endsection

@section('content')
<div class="login-form__content">
    <div class="login-form__heading">
        <h2>ログイン</h2>
    <form class="login-form"  method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <div class="login-form__group">
            <div class="login-form__group-title">
                <span class="login-form__label--item">ユーザー名/メールアドレス</span>
            </div>
            <div class="login-form__group-content">
                <div class="login-form__input--text">
                    <input class="login-form__input" type="email" name="email" value="{{ old('email') }}" />
                </div>
                @error('email')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="login-form__group">
            <div class="login-form__group-title">
                <span class="login-form__label--item">パスワード</span>
            </div>
            <div class="login-form__group-content">
                <div class="login-form__input--text">
                    <input class="login-form__input" type="password" name="password" />
                </div>
                @error('password')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form__group--btn">
            <button type="submit" class="login-btn">ログインする</button>
        </div>
    </form>
    <div class="form__group--register">
        <a href="/register" class="register-btn">会員登録はこちら</a>
    </div>
</div>

@endsection('content')