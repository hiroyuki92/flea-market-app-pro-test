@extends('layouts.app')

@section('content')
<main class="container center">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="border: none;">
                <div class="card-body d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
                    @if (session('message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    <p style="font-weight: bold;">登録していただいたメールアドレスに認証メールを送付しました。</p>
                    <p style="font-weight: bold;">メール認証を完了してください。</p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0" style="text-decoration: none;">
                            認証メールを再送する
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection