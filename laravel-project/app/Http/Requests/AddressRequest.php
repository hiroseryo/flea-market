<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:png',
            'name' => 'required|string',
            'postcode' => 'required|string|min:8',
            'address' => 'required|string',
            'building' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => '画像ファイルはPNG形式のみ対応しています',
            'name.required' => '名前を入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'postcode.min' => '郵便番号はハイフンありで8文字以内で入力してください',
            'address.required' => '住所を入力してください',
            'building.required' => '建物名を入力してください',
        ];
    }
}
