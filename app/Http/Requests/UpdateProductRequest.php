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
        $productId = $this->route('id');

        return [
            'vendor_id' => 'sometimes|exists:vendors,id',
            'main_category_id' => 'sometimes|nullable|exists:main_categories,id',
            'main_category_id_2' => 'sometimes|nullable|exists:main_categories,id',
            'category_id' => 'sometimes|exists:categories,id',
            'category_id_2' => 'sometimes|nullable|exists:categories,id',
            'brand_id' => 'sometimes|exists:brands,id',
            'branch_id' => 'sometimes|nullable|exists:vendor_branches,id',
            'name' => 'sometimes|string|max:255',
            'slug' => ['sometimes', 'nullable', 'string', Rule::unique('products', 'slug')->ignore($productId)],
            'stock' => 'sometimes|integer|min:0',
            'short_description' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'discount_percentage' => 'sometimes|nullable|numeric|min:0|max:100',
            'discounted_price' => 'sometimes|nullable|numeric|min:0',
            'discount_start' => 'sometimes|nullable|date',
            'discount_end' => 'sometimes|nullable|date|after_or_equal:discount_start',
            'is_active' => 'sometimes|boolean',
            'features' => 'sometimes|nullable|array',
            'features.*' => 'nullable|string',
            'product_info' => 'sometimes|nullable|array',
            'product_info.*' => 'nullable|string',
            'usage_description' => 'sometimes|nullable|string',
            'parts_description' => 'sometimes|nullable|string',
            'material_description' => 'sometimes|nullable|string',
            'dimensions' => 'sometimes|nullable|string|max:255',
            'weight' => 'sometimes|nullable|string|max:255',
            'volume' => 'sometimes|nullable|string|max:255',
            'available_sizes' => 'sometimes|nullable|array',
            'available_sizes.*' => 'nullable|string',
            'available_colors' => 'sometimes|nullable|array',
            'available_colors.*' => 'nullable|string',
            'origin_country' => 'sometimes|nullable|string|max:255',
            'manufacturer' => 'sometimes|nullable|string|max:255',
            'model' => 'sometimes|nullable|string|max:255',
            'expiry_period' => 'sometimes|nullable|string|max:255',
            'subcategories_level1' => 'sometimes|nullable|string|max:255',
            'subcategory_level2' => 'sometimes|nullable|string|max:255',
            'auto_discount_end_date' => 'sometimes|nullable|date',
            'has_deposit' => 'sometimes|nullable|boolean',
            'deposit_percentage' => 'sometimes|nullable|numeric|min:0|max:100',
            'piece_type' => 'sometimes|nullable|string|max:255',
            'pieces_per_package' => 'sometimes|nullable|integer|min:0',
            'requires_delivery_otp' => 'sometimes|nullable|boolean',

            'free_shipping' => ['sometimes', 'nullable', Rule::in(['0', 'available', 'price'])],
            'free_shipping_min_order' => 'sometimes|nullable|numeric|min:0',
            'free_shipping_price' => 'sometimes|nullable|numeric|min:0',
            'shipment_type' => 'sometimes|nullable|string|max:255',
            'shipment_description' => 'sometimes|nullable|string',
            'shipment_dimensions' => 'sometimes|nullable|string|max:255',
            'shipment_weight' => 'sometimes|nullable|string|max:255',
            'storage_conditions' => 'sometimes|nullable|array',
            'storage_conditions.*' => 'nullable|string',
            'delivery_zones' => 'sometimes|nullable|array',
            'delivery_zones.*' => 'nullable',
            'delivery_options' => 'sometimes|nullable|array',
            'delivery_options.*' => 'nullable|string',
            'package_length' => 'sometimes|nullable|numeric|min:0',
            'package_width' => 'sometimes|nullable|numeric|min:0',
            'package_height' => 'sometimes|nullable|numeric|min:0',
            'package_weight' => 'sometimes|nullable|numeric|min:0',
            'is_returnable' => 'sometimes|nullable|boolean',
            'return_fee' => 'sometimes|nullable|numeric|min:0',
            'return_validity' => 'sometimes|nullable|integer|min:0',

            'shipping_profile' => 'sometimes|nullable|array',
            'shipping_profile.shipment_type' => 'nullable|string|max:255',
            'shipping_profile.shipment_description' => 'nullable|string',
            'shipping_profile.shipment_dimensions' => 'nullable|string|max:255',
            'shipping_profile.shipment_weight' => 'nullable|string|max:255',
            'shipping_profile.storage_conditions' => 'nullable|array',
            'shipping_profile.storage_conditions.*' => 'nullable|string',
            'shipping_profile.delivery_zones' => 'nullable|array',
            'shipping_profile.delivery_zones.*' => 'nullable',
            'shipping_profile.delivery_options' => 'nullable|array',
            'shipping_profile.delivery_options.*' => 'nullable|string',
            'shipping_profile.package_length' => 'nullable|numeric|min:0',
            'shipping_profile.package_width' => 'nullable|numeric|min:0',
            'shipping_profile.package_height' => 'nullable|numeric|min:0',
            'shipping_profile.package_weight' => 'nullable|numeric|min:0',

            'return_policy' => 'sometimes|nullable|array',
            'return_policy.is_returnable' => 'nullable|boolean',
            'return_policy.return_fee' => 'nullable|numeric|min:0',
            'return_policy.return_validity' => 'nullable|integer|min:0',

            'shipping_fee_policy' => 'sometimes|nullable|array',
            'shipping_fee_policy.free_shipping' => ['nullable', Rule::in(['0', 'available', 'price'])],
            'shipping_fee_policy.free_shipping_min_order' => 'nullable|numeric|min:0',
            'shipping_fee_policy.free_shipping_price' => 'nullable|numeric|min:0',

            'translations' => 'sometimes|nullable|array',
            'translations.*.name' => 'required_with:translations|string|max:255',
            'translations.*.short_description' => 'nullable|string',
            'translations.*.description' => 'nullable|string',

            'media' => 'sometimes|array|min:1',
            'media.*.type' => ['sometimes', Rule::in(array_column(ProductMediaType::cases(), 'value'))],
            'media.*.file' => [
                'sometimes',
                'file',
                Rule::when(
                    fn ($input) => data_get($input, 'type') === ProductMediaType::IMAGE->value,
                    ['mimes:jpeg,png,jpg,gif', 'max:2048']
                ),
                Rule::when(
                    fn ($input) => data_get($input, 'type') === ProductMediaType::VIDEO->value,
                    ['mimes:mp4,avi,mov,mkv', 'max:20480']
                ),
            ],
            'media.*.position' => 'nullable|integer|min:0',

            'variants' => 'sometimes|nullable|array',
            'variants.*.color_id' => 'nullable|exists:product_colors,id',
            'variants.*.size_id' => 'nullable|exists:product_sizes,id',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0',

            'related_products' => 'sometimes|nullable|array',
            'related_products.*' => 'exists:products,id',
        ];
    }
}
