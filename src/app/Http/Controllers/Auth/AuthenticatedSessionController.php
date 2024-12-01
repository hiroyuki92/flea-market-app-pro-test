<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * ユーザーをログインさせる
     *
     * @param  \App\Http\Requests\LoginRequest  $request
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

            /* // 初回ログインの場合、会員登録画面にリダイレクト
            if ($user->first_login) {
                // 初回ログイン後、first_loginフラグをfalseに更新
                $user->first_login = false;
                $user->save();

                return redirect()->route('profile.edit');  // プロフィール設定画面にリダイレクト
            } */

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
}
