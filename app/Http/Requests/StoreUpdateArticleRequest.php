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
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category_id' => 'required_without:new_category|nullable|exists:categories,id',
            'new_category' => 'required_without:category_id|nullable|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('category_id') && $this->filled('new_category')) {
                $validator->errors()->add('category', 'You can either select an existing category OR create a new one, not both.');
            }
        });
    }
    


}
