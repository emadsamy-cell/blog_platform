<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|in:Technology,Lifestyle,Education', // Predefined categories
        ];
    }

    public function messages()
    {
        return [
            'category.in' => 'The category must be Technology, Lifestyle, or Education.',
        ];
    }
}
