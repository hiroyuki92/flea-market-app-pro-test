<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',  // カテゴリーは必須、存在するカテゴリー
            'name' => 'required|string|max:255',  // 商品名は必須、最大255文字
            'brand' => 'required|string|max:255',  // ブランド名は任意
            'price' => 'required|numeric|min:0',  // 商品価格は必須、数値で0円以上
            'description' => 'required|string|max:255',  // 商品説明は必須、最大255文字
            'image' => 'required|image|mimes:jpeg,png|max:2048',  // 商品画像は必須、画像形式、最大2MB
            'condition' => 'required|integer',  // 商品の状態は必須、整数
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
            'category_id.required' => 'カテゴリーは必須です。',
            'name.required' => '商品名は必須です。',
            'name.string' => '商品名は文字列で入力してください。',
            'name.max' => '商品名は255文字以内で入力してください。',
            'brand.required' => 'ブランド名は必須です。',
            'brand.string' => 'ブランド名は文字列で入力してください。',
            'brand.max' => 'ブランド名は255文字以内で入力してください。',
            'price.required' => '値段は必須です。',
            'price.numeric' => '商品価格は数値で入力してください。',
            'price.min' => '商品価格は0円以上で入力してください。',
            'description.required' => '商品説明は必須です。',
            'description.string' => '商品説明は文字列で入力してください。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'image.required' => '画像は必須です。',
            'image.image' => '画像ファイルを選択してください。',
            'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください。',
            'condition.required' => '商品の状態は必須です。',
        ];
    }
}

