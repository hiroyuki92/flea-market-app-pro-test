<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;

class RegisteredUserController extends Controller
{
    /**
     * ユーザー登録フォームを表示
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * 新しいユーザーを登録
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // メール送信: ユーザーにメール認証通知を送信
    try {
        $user->sendEmailVerificationNotification();
    } catch (\Exception $e) {
        \Log::error('Verification email could not be sent: ' . $e->getMessage());
    }

    auth()->login($user);

    return redirect()->route('verification.notice');
        /* return redirect()->route('profile.edit'); */
    }
}
