<?php

namespace App\Http\Requests;

use App\Enum\ProductMediaType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => 'required|exists:vendors,id',
            'category_id' => 'required|exists:categories,id',
            'brand_id'=> 'required|exists:brands,id',
            'name' => 'required|string',
            'stock' => 'required|integer',
            'slug' => 'required|string|unique:products,slug',
            'short_description' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'is_active' => 'required|boolean',

            // media
            'media' => 'required|array',
            'media.*.type' => ['required', Rule::in(array_column(ProductMediaType::cases(), 'value'))],

            'media.*.file' => [
                'required',
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
            'variants.*.stock' => 'required|integer',

            'related_products' => 'nullable|array',
            'related_products.*' => 'exists:products,id',
        ];
    }
}
