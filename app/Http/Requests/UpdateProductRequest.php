<?php

namespace App\Http\Requests;

use App\Enum\ProductMediaType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id'=> 'required|exists:brands,id',
            'name' => 'sometimes|string',
            'stock' => 'sometimes|integer',
            'slug' => 'sometimes|string|unique:products,slug',
            'short_description' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'is_active' => 'sometimes|boolean',

            // media
            'media' => 'sometimes|array',
            'media.*.type' => ['sometimes', Rule::in(array_column(ProductMediaType::cases(), 'value'))],

            'media.*.file' => [
                'sometimes',
                'file',
                Rule::when(
                    fn($input) => data_get($input, 'type') === ProductMediaType::IMAGE->value,
                    ['mimes:jpeg,png,jpg,gif', 'max:2048']
                ),
                Rule::when(
                    fn($input) => data_get($input, 'type') === ProductMediaType::VIDEO->value,
                    ['mimes:mp4,avi,mov,mkv', 'max:20480']
                ),
            ],

            'media.*.position' => 'nullable|integer',

            // variants
            'variants' => 'nullable|array',
            'variants.*.color_id' => 'nullable|exists:product_colors,id',
            'variants.*.size_id' => 'nullable|exists:product_sizes,id',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.stock' => 'sometimes|integer',

            'related_products' => 'nullable|array',
            'related_products.*' => 'exists:products,id',
        ];
    }
}
