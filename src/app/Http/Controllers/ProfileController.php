<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile');
    }

    /**
     * ユーザーのプロフィール編集フォームを表示
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();  // ログインしているユーザーを取得

        return view('edit', compact('user'));  // 編集フォームを表示
    }

    /**
     * ユーザーのプロフィールを更新
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();  // ログインしているユーザーを取得

        // 画像がアップロードされている場合
        if ($request->hasFile('profile_image')) {
            // 古い画像を削除（もし存在すれば）
            if ($user->profile_image) {
                Storage::delete('public/profile_images/' . $user->profile_image);
            }

            // 新しい画像を保存
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');

            // プロフィール画像のパスを保存
            $user->profile_image = basename($imagePath);  // ストレージに保存されたファイル名を保存
        }

        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->address_line = $request->address_line;
        $user->building = $request->building;

        $user->save();

        return redirect()->route('profile.show');
    }
}
