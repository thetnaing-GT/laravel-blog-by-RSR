<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;


use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateArticleRequest extends FormRequest
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
            'title' => 'required',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
            'new_category' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->category_id && !$this->new_category) {
                $validator->errors()->add('category_id', 'Please select a category or enter a new one.');
            }
        });
    }


    


}
