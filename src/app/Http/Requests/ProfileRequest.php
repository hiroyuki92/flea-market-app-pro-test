<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'profile_image' => 'required|image|mimes:jpeg,png|max:2048', // 画像は必須、jpeg, pngの形式で最大2MB
        ];
    }
    /**
     * バリデーションエラーメッセージをカスタマイズする
     *
     * @return array
     */
    public function messages()
    {
        return [
            'profile_image.required' => 'プロフィール画像は必須です。',
            'profile_image.image' => '有効な画像ファイルを選択してください。',
            'profile_image.mimes' => '画像はjpeg, png形式で選択してください。',
            'profile_image.max' => '画像のサイズは2MB以下にしてください。',
        ];
    }
}
