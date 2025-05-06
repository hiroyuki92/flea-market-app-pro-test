<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * ユーザーをログインさせる
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // ログイン試行
        if (Auth::attempt([
            'email' => $validated['email'],  // バリデーションされたメールアドレス
            'password' => $validated['password'],  // バリデーションされたパスワード
        ], $request->filled('remember'))) {
            // ログイン成功した場合、元々アクセスしようとしていたページにリダイレクト

            $user = Auth::user();  // ログインユーザーを取得

            // それ以外の場合は商品一覧ページにリダイレクト
            return redirect()->intended('/');  // 商品一覧ページなど
        }

        // ログイン失敗した場合
        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません。'],
        ]);
    }

    /**
     * ユーザーをログアウトさせる
     */
    public function destroy(Request $request)
    {
        auth()->logout();  // ユーザーをログアウト
        $request->session()->invalidate();  // セッションを無効化
        $request->session()->regenerateToken();  // CSRFトークンを再生成

        return redirect('/login');  // ログアウト後のリダイレクト先
    }

    public function loginWithDummyUser()
    {
        $user = User::factory()->create();
        Auth::login($user);

        return redirect()->route('index');
    }
}
