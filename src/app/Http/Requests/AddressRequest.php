<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'name' => 'required|string|max:255', // ユーザー名は必須、最大255文字
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/', // 郵便番号は必須、ハイフンを含む8文字の形式
            'address' => 'required|string|max:255', // 住所は必須、最大255文字
            'building' => 'required|string|max:255', // 建物名は必須、最大255文字
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '名前は必須です。',
            'postal_code.required' => '郵便番号は必須です。',
            'postal_code.regex' => '郵便番号は「XXX-XXXX」の形式で入力してください。',
            'address.required' => '住所は必須です。',
            'building.required' => '建物名は必須です。',
        ];
    }
}
