@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">メールアドレスの確認</div>

                <div class="card-body">
                    @if (session('message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    メールアドレスの確認が必要です。
                    確認用メールをご確認ください。

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                            確認メールを再送信する
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection