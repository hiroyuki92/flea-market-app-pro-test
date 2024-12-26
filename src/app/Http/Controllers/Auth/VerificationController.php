<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function show()
    {
        return view('auth.verify');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('profile.edit')->with('verified', true);  // リダイレクト先を適切に設定
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '確認メールを再送信しました。');
    }
}

/* class VerificationController extends Controller
{
    // 認証待ち画面を表示
    public function show()
    {
        return view('auth.verify');
    }

    // メール認証処理
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect('/home'); // 認証後にリダイレクトする先を指定
    }

    // 認証メールの再送信
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '認証メールを再送しました。');
    }
} */
