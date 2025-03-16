<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'category' => 'sometimes|string|in:Technology,Lifestyle,Education',
        ];
    }

    public function messages()
    {
        return [
            'category.in' => 'The category must be Technology, Lifestyle, or Education.',
        ];
    }
}
