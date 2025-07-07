<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Or add your authorization logic
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:tags,name,'.$this->route('tag').'|max:255',
        ];
    }
    
    // Optional: Customize error messages
    public function messages()
    {
        return [
            'name.required' => 'The tag name is required',
            'name.unique' => 'This tag already exists',
            'name.max' => 'Tag name cannot exceed 255 characters',
        ];
    }
}