<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:active,inactive'],
            'category' => ['required', 'string', 'max:100'],
            'delivery_time' => ['required', 'integer', 'min:1'],
            'revisions' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The service title is required.',
            'title.max' => 'The service title cannot exceed 255 characters.',
            'description.required' => 'The service description is required.',
            'description.max' => 'The service description cannot exceed 1000 characters.',
            'price.required' => 'The service price is required.',
            'price.numeric' => 'The service price must be a number.',
            'price.min' => 'The service price must be greater than 0.',
            'status.in' => 'The service status must be either active or inactive.',
            'category.required' => 'The service category is required.',
            'delivery_time.required' => 'The delivery time is required.',
            'delivery_time.min' => 'The delivery time must be at least 1 day.',
            'revisions.required' => 'The number of revisions is required.',
            'revisions.min' => 'The number of revisions cannot be negative.',
        ];
    }
} 