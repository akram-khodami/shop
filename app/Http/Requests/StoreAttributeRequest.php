<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttributeRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:attributes,name',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'is_public' => 'boolean',
            'type' => [
                'required', Rule::in(['text', 'number', 'select', 'checkbox', 'date'])
            ],
            'order' => 'integer|min:0',
            'unit' => 'nullable|string|max:255',
            'options' => [
                Rule::requiredIf(function () {
                    return $this->input('type') === 'select';
                }),
                'array', 'min:1'
            ],
            'options.*' => 'string|distinct|min:1'
        ];
    }
}
