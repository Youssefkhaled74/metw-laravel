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
            'main_category_id' => 'nullable|exists:main_categories,id',
            'main_category_id_2' => 'nullable|exists:main_categories,id',
            'category_id' => 'required|exists:categories,id',
            'category_id_2' => 'nullable|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'branch_id' => 'nullable|exists:vendor_branches,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'stock' => 'required|integer|min:0',
            'short_description' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discounted_price' => 'nullable|numeric|min:0',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'is_active' => 'required|boolean',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'product_info' => 'nullable|array',
            'product_info.*' => 'nullable|string',
            'usage_description' => 'nullable|string',
            'parts_description' => 'nullable|string',
            'material_description' => 'nullable|string',
            'dimensions' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'volume' => 'nullable|string|max:255',
            'available_sizes' => 'nullable|array',
            'available_sizes.*' => 'nullable|string',
            'available_colors' => 'nullable|array',
            'available_colors.*' => 'nullable|string',
            'origin_country' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'expiry_period' => 'nullable|string|max:255',
            'subcategories_level1' => 'nullable|string|max:255',
            'subcategory_level2' => 'nullable|string|max:255',
            'auto_discount_end_date' => 'nullable|date',
            'has_deposit' => 'nullable|boolean',
            'deposit_percentage' => 'nullable|numeric|min:0|max:100',
            'piece_type' => 'nullable|string|max:255',
            'pieces_per_package' => 'nullable|integer|min:0',
            'requires_delivery_otp' => 'nullable|boolean',

            'free_shipping' => ['nullable', Rule::in(['0', 'available', 'price'])],
            'free_shipping_min_order' => 'nullable|numeric|min:0',
            'free_shipping_price' => 'nullable|numeric|min:0',
            'shipment_type' => 'nullable|string|max:255',
            'shipment_description' => 'nullable|string',
            'shipment_dimensions' => 'nullable|string|max:255',
            'shipment_weight' => 'nullable|string|max:255',
            'storage_conditions' => 'nullable|array',
            'storage_conditions.*' => 'nullable|string',
            'delivery_zones' => 'nullable|array',
            'delivery_zones.*' => 'nullable',
            'delivery_options' => 'nullable|array',
            'delivery_options.*' => 'nullable|string',
            'package_length' => 'nullable|numeric|min:0',
            'package_width' => 'nullable|numeric|min:0',
            'package_height' => 'nullable|numeric|min:0',
            'package_weight' => 'nullable|numeric|min:0',
            'is_returnable' => 'nullable|boolean',
            'return_fee' => 'nullable|numeric|min:0',
            'return_validity' => 'nullable|integer|min:0',

            'shipping_profile' => 'nullable|array',
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

            'return_policy' => 'nullable|array',
            'return_policy.is_returnable' => 'nullable|boolean',
            'return_policy.return_fee' => 'nullable|numeric|min:0',
            'return_policy.return_validity' => 'nullable|integer|min:0',

            'shipping_fee_policy' => 'nullable|array',
            'shipping_fee_policy.free_shipping' => ['nullable', Rule::in(['0', 'available', 'price'])],
            'shipping_fee_policy.free_shipping_min_order' => 'nullable|numeric|min:0',
            'shipping_fee_policy.free_shipping_price' => 'nullable|numeric|min:0',

            'translations' => 'nullable|array',
            'translations.*.name' => 'required_with:translations|string|max:255',
            'translations.*.short_description' => 'nullable|string',
            'translations.*.description' => 'nullable|string',

            'media' => 'required|array|min:1',
            'media.*.type' => ['required', Rule::in(array_column(ProductMediaType::cases(), 'value'))],
            'media.*.file' => [
                'required',
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

            'variants' => 'nullable|array',
            'variants.*.color_id' => 'nullable|exists:product_colors,id',
            'variants.*.size_id' => 'nullable|exists:product_sizes,id',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0',

            'related_products' => 'nullable|array',
            'related_products.*' => 'exists:products,id',
        ];
    }
}
