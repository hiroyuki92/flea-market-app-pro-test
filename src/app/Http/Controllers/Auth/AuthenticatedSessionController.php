<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
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
