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
            'category_id' => 'required|exists:categories,id',  // 商品カテゴリー
            'name' => 'required',  // 商品名
            'brand' => 'required',  // ブランド名
            'price' => 'required|numeric|min:0',  // 商品価格（必須、0円以上の数値）
            'description' => 'required|string|max:255',  // 商品説明（必須、最大255文字）
            'image_url' => 'required|image|mimes:jpeg,png|max:2048',  // 商品画像（必須、拡張子jpegかpng、最大2MB）
            'condition' => 'required|integer',  // 商品の状態（必須）
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
            'category_id.required' => '商品カテゴリーは必須です。',
            'category_id.exists' => '選択したカテゴリーが無効です。',
            'name.required' => '商品名は必須です。',
            'brand.required' => '商品名は必須です。',
            'price.required' => '商品価格は必須です。',
            'price.numeric' => '商品価格は数値で入力してください。',
            'price.min' => '商品価格は0円以上で入力してください。',
            'description.required' => '商品説明は必須です。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'image_url.required' => '商品画像は必須です。',
            'image_url.image' => '画像ファイルを選択してください。',
            'image_url.mimes' => '画像はjpegまたはpng形式でアップロードしてください。',
            'condition.required' => '商品の状態は必須です。',
        ];
    }
}
