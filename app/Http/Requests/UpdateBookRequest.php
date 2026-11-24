<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'published_year' => ['required', 'integer', 'min:1000', 'max:'.date('Y')],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }
}
