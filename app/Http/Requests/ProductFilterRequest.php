<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductFilterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'properties' => 'sometimes|array',
            'properties.*' => 'array',
            'properties.*.*' => 'string',
            'price' => 'sometimes|array',
            'price.min' => 'numeric|min:0',
            'price.max' => 'numeric|gt:price.min',
            'quantity' => 'sometimes|array',
            'quantity.min' => 'integer|min:0',
            'quantity.max' => 'integer|gt:quantity.min',
            'sort_by' => ['sometimes', 'string', Rule::in(['id', 'name', 'price', 'quantity'])],
            'sort_direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'properties.*.*.string' => 'Значения свойств должны быть строками',
            'price.min.numeric' => 'Минимальная цена должна быть числом',
            'price.max.numeric' => 'Максимальная цена должна быть числом',
            'price.max.gt' => 'Максимальная цена должна быть больше минимальной',
            'quantity.min.integer' => 'Минимальное количество должно быть целым числом',
            'quantity.max.integer' => 'Максимальное количество должно быть целым числом',
            'quantity.max.gt' => 'Максимальное количество должно быть больше минимального',
            'sort_by.in' => 'Недопустимое поле для сортировки',
            'sort_direction.in' => 'Направление сортировки должно быть "asc" или "desc"',
            'per_page.integer' => 'Количество элементов на странице должно быть целым числом',
            'per_page.min' => 'Количество элементов на странице должно быть не менее 1',
            'per_page.max' => 'Количество элементов на странице должно быть не более 100',
        ];
    }
}
