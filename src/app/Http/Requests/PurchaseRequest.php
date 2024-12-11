<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address_line' => 'required|string|max:255',
            'building' => 'required|string|max:255',
            /* 'payment_method' => 'required|in:convenience_store,credit_card',  // 支払い方法は必須、選べるのは「コンビニ支払い」または「カード支払い」 */
        ];
    }
    
    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'shipping_address_id.required' => '配送先住所を選択してください。',
            /* 'shipping_address_id.exists' => '指定された配送先住所が存在しません。', */
        ];
    }
}
