<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'img' => 'required|image|mimes:png',
            'condition_id' => 'required|exists:conditions,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'name' => 'required|string',
            'description' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'img.required' => '商品画像は必須です',
            'img.image' => '画像ファイルを選択してください',
            'img.mimes' => '画像ファイルはPNG形式のみアップロード可能です',
            'condition_id.required' => '商品の状態を選択してください',
            'condition_id.exists' => '選択された商品の状態が無効です',
            'categories.required' => '少なくとも1つのカテゴリーを選択してください',
            'categories.array' => 'カテゴリーの形式が無効です',
            'categories.*.exists' => '選択されたカテゴリーが無効です',
            'name.required' => '商品名を入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'price.required' => '価格を入力してください',
            'price.integer' => '価格は整数で入力してください',
            'price.min' => '価格は0円以上で入力してください',
        ];
    }
}
