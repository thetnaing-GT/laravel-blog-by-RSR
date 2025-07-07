<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set to true unless you need authorization logic
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:tags,name',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tag name is required',
            'name.unique' => 'This tag already exists',
            'name.max' => 'Tag name cannot exceed 255 characters',
        ];
    }
}