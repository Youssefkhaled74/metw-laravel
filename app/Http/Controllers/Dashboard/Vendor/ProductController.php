<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ConsignmentType;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductMedia;
use App\Models\ProductVariant;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\MainCategory;
use App\Models\VendorBranch;
use App\Models\Zone;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    public function index()
    {
        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'brand_id' => ['nullable', 'string'],
            'main_category_id' => ['nullable', 'string'],
            'category_id' => ['nullable', 'string'],
            'status' => ['nullable', 'in:all,active,inactive'],
            'sort_by' => ['nullable', 'in:product_number,name,category,brand,price,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $productsQuery = Product::withoutGlobalScope('active')
            ->where('vendor_id', auth('vendor')->id())
            ->with(['category', 'media', 'brand', 'translations']);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $productsQuery->where(function ($query) use ($search) {
                $query->where('product_number', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%")
                    ->orWhereHas('translations', function ($translationQuery) use ($search) {
                        $translationQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('brand', function ($brandQuery) use ($search) {
                        $brandQuery->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($validated['brand_id']) && $validated['brand_id'] !== 'all') {
            $productsQuery->where('brand_id', (int) $validated['brand_id']);
        }

        if (!empty($validated['main_category_id']) && $validated['main_category_id'] !== 'all') {
            $productsQuery->where('main_category_id', (int) $validated['main_category_id']);
        }

        if (!empty($validated['category_id']) && $validated['category_id'] !== 'all') {
            $productsQuery->where('category_id', (int) $validated['category_id']);
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $productsQuery->where('is_active', $validated['status'] === 'active');
        }

        if ($sortBy === 'name') {
            $locale = app()->getLocale();
            $productsQuery->leftJoin('product_translations as pt', function ($join) use ($locale) {
                $join->on('products.id', '=', 'pt.product_id')
                    ->where('pt.locale', '=', $locale);
            })
                ->select('products.*')
                ->orderBy('pt.name', $sortDir);
        } elseif ($sortBy === 'category') {
            $productsQuery->leftJoin('categories as c', 'products.category_id', '=', 'c.id')
                ->select('products.*')
                ->orderBy('c.name', $sortDir);
        } elseif ($sortBy === 'brand') {
            $brandNameColumn = app()->getLocale() === 'ar' ? 'b.name_ar' : 'b.name_en';
            $productsQuery->leftJoin('brands as b', 'products.brand_id', '=', 'b.id')
                ->select('products.*')
                ->orderBy($brandNameColumn, $sortDir);
        } else {
            $productsQuery->orderBy("products.{$sortBy}", $sortDir);
        }

        $products = $productsQuery
            ->paginate(10)
            ->appends(request()->query());

        $brands = Brand::withoutGlobalScope('active')->orderBy('name_en')->get(['id', 'name_en', 'name_ar']);
        $mainCategories = MainCategory::withoutGlobalScope('active')->get(['id', 'name']);
        $categories = Category::withoutGlobalScope('active')->get(['id', 'name']);

        return view('dashboard.vendor.products.index', compact('products', 'brands', 'mainCategories', 'categories'));
    }


    public function create()
    {
        $currentLocale = app()->getLocale();

        // Option 1: Eager load translations
        $mainCategories = MainCategory::with(['translations' => function($query) use ($currentLocale) {
            $query->where('locale', $currentLocale);
        }])->get();

        $categories = Category::with(['translations' => function($query) use ($currentLocale) {
            $query->where('locale', $currentLocale);
        }])->get();

        // Option 2: If you prefer simpler, just get all and let the accessor handle it
        // $mainCategories = MainCategory::all();
        // $categories = Category::all();

        $colors = ProductColor::all();
        $sizes = ProductSize::all();
        $brands = Brand::all();
        $zones = Zone::all();
        $branches = VendorBranch::where('vendor_id', auth('vendor')->id())->get();
        $consignment_types = ConsignmentType::all();

        return view('dashboard.vendor.products.create', compact('mainCategories', 'categories', 'colors', 'sizes', 'brands', 'zones', 'consignment_types', 'branches'));
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'translations' => 'required|array',
            'translations.en.name' => 'required|string|max:255',
            'translations.en.description' => 'required|string',
            'translations.ar.name' => 'required|string|max:255',
            'translations.ar.description' => 'required|string',

            'price' => 'required|numeric|min:0',
            'brand_id' => 'nullable|exists:brands,id',

            'main_category_id'   => 'required|exists:main_categories,id',
            'main_category_id_2' => 'nullable|exists:main_categories,id',
            'category_id'        => 'required|exists:categories,id',
            'category_id_2'      => 'nullable|exists:categories,id',

            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'videos.*' => 'file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
            // 'color_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'extra_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',

            'variants' => 'nullable|array',
            'variants.*.color_id' => 'nullable|exists:product_colors,id',
            'variants.*.size_id' => 'nullable|exists:product_sizes,id',
            'variants.*.price'   => 'nullable|numeric|min:0',
            'variants.*.stock'   => 'nullable|integer|min:0',
            'variants.*.color_images' => 'nullable|array',

            // Extra details
            'features'            => 'nullable|string',
            'product_info'        => 'nullable|string',
            'usage_description'   => 'nullable|string',
            'parts_description'   => 'nullable|string',
            'material_description' => 'nullable|string',
            'dimensions'          => 'nullable|string',
            'weight'              => 'nullable|string',
            'volume'              => 'nullable|string',
            'available_sizes'     => 'nullable|text',
            'available_colors'    => 'nullable|array',
            'origin_country'      => 'nullable|string|max:255',
            'manufacturer'        => 'nullable|string|max:255',
            'model'               => 'nullable|string|max:255',
            'expiry_period'       => 'nullable|string|max:255',
            'requires_delivery_otp' => 'nullable|boolean',

            // Discounts & Shipping
            'discount_percentage'     => 'nullable|numeric|min:0|max:100',
            'discount_start'          => 'nullable|date|after_or_equal:today',
            'discount_end'            => 'nullable|date|after_or_equal:discount_start|after_or_equal:today',
            'free_shipping'           => 'nullable|string|in:0,available,price',
            'free_shipping_min_order' => 'nullable|numeric|min:0',
            'shipment_type'           => 'nullable|string|max:255',
            'shipment_description'    => 'nullable|string',
            'shipment_dimensions'     => 'nullable|string',
            'shipment_weight'         => 'nullable|string',

            'piece_type' => 'nullable|in:small,medium,large,xlarge',
            'pieces_per_package'  => 'nullable|numeric|min:0',

            // Storage & Delivery
            'storage_conditions' => 'nullable|array',
            'delivery_zones'     => 'nullable|array',
            'delivery_options'   => 'nullable|array',

            'is_active' => 'boolean',
            'stock'     => 'nullable|integer|min:0',

            'subcategories_level1' => 'nullable|string|max:255',
            'subcategory_level2'  => 'nullable|string|max:255',
            // 'colors'              => 'nullable|array',
            // 'colors.*'            => 'exists:product_colors,id',
            'auto_discount_end_date' => 'nullable|date',
            'free_shipping_price' => 'nullable|numeric|min:0',
            'package_length'      => 'nullable|numeric|min:0',
            'package_width'       => 'nullable|numeric|min:0',
            'package_height'      => 'nullable|numeric|min:0',
            'package_weight'      => 'nullable|numeric|min:0',

            // Return fields
            'is_returnable'       => 'nullable|boolean',
            'return_fee'           => 'nullable|numeric|min:0',
            'return_validity'      => 'nullable|numeric|min:0',
            'branch_id'           => 'required|exists:vendor_branches,id',

            'has_deposit' => 'nullable|boolean',
            'deposit_percentage' => 'nullable|required_if:has_deposit,1|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        if (!empty($validated['has_deposit'])) {
            $price = $validated['price'];

            // Calculate deposit based on amount or percentage
            if (!empty($validated['deposit_amount'])) {
                $depositAmount = $validated['deposit_amount'];
            } elseif (!empty($validated['deposit_percentage'])) {
                $depositAmount = $price * ($validated['deposit_percentage'] / 100);
            } else {
                $depositAmount = 0;
            }

            // Calculate final price after deposit
            // $validated['final_price_after_deposit'] = $price - $depositAmount;

            // // Ensure final price is not negative
            // if ($validated['final_price_after_deposit'] < 0) {
            //     $validated['final_price_after_deposit'] = 0;
            // }
        } else {
            $validated['has_deposit'] = false;
            $validated['deposit_amount'] = null;
            $validated['deposit_percentage'] = null;
            $validated['final_price_after_deposit'] = null;
        }

        if ($request->has('storage_conditions')) {
            $validated['storage_conditions'] = json_encode($request->input('storage_conditions'), JSON_UNESCAPED_UNICODE);
        }

        $validated['vendor_id'] = auth('vendor')->id();
        $validated['is_active'] = $request->has('is_active');
        $validated['is_returnable'] = $request->has('is_returnable');
        $validated['sku'] = strtoupper(uniqid());
        $validated['requires_delivery_otp'] = $request->has('requires_delivery_otp');
        // $validated['name'] = $validated['translations']['en']['name'];
        // $validated['description'] = $validated['translations']['en']['description'];

        // Slug
        $validated['slug'] = Str::slug($validated['translations']['en']['name'] . ' ' . $validated['sku']);

        // Stock logic
        $variants = $validated['variants'] ?? [];
        $hasValidVariants = false;

        // Check if there are any valid variants (with color_id, size_id, or stock)
        if (!empty($variants)) {
            foreach ($variants as $variant) {
                if (!empty($variant['color_id']) || !empty($variant['size_id']) ||
                    (isset($variant['stock']) && $variant['stock'] > 0)) {
                    $hasValidVariants = true;
                    break;
                }
            }
        }

        if ($hasValidVariants) {
            // Calculate stock from variants if they exist
            $validated['stock'] = array_sum(array_column($variants, 'stock'));
        } else {
            // Use stock from input field if no variants
            $validated['stock'] = $validated['stock'] ?? 0;
        }

        // Discounted price
        if (!empty($validated['discount_percentage'])) {
            $validated['discounted_price'] = $validated['price'] - ($validated['price'] * $validated['discount_percentage'] / 100);
        }

        // Encode array fields to JSON
        foreach (
            [
                'features',
                'product_info',
                'available_sizes',
                'available_colors',
                'delivery_zones',
                'delivery_options',
                'storage_conditions',
            ] as $field
        ) {
            if ($request->has($field)) {
                $value = $request->input($field);
                $validated[$field] = is_array($value)
                    ? json_encode($value, JSON_UNESCAPED_UNICODE)
                    : $value;
            }
        }


        // Create product
        $product = Product::create($validated);

        // Translations
        foreach ($validated['translations'] as $locale => $translation) {
            $product->translations()->create([
                'locale' => $locale,
                'name'   => $translation['name'],
                'slug'   => Str::slug($translation['name'] . ' ' . $validated['sku']),
                'short_description' => $translation['short_description'] ?? null,
                'description'       => $translation['description'],
            ]);
        }

        // Media uploads
        $imagesCsv = uploadImages($request, 'images', 'storage/products/images');
        if ($imagesCsv) {
            foreach (explode(',', $imagesCsv) as $imgPath) {
                $product->media()->create(['type' => 'image', 'url' => trim($imgPath)]);
            }
        }

        $videosCsv = uploadImages($request, 'videos', 'storage/products/videos');
        if ($videosCsv) {
            foreach (explode(',', $videosCsv) as $vidPath) {
                $product->media()->create(['type' => 'video', 'url' => trim($vidPath)]);
            }
        }

        $extraImagesCsv = uploadImages($request, 'extra_images', 'storage/products/extra_images');
        if ($extraImagesCsv) {
            foreach (explode(',', $extraImagesCsv) as $imgPath) {
                $product->media()->create(['type' => 'extra_image', 'url' => trim($imgPath)]);
            }
        }

        // $colorImagesCsv = uploadImages($request, 'color_images', 'storage/products/color_images');
        // if ($colorImagesCsv) {
        //     foreach (explode(',', $colorImagesCsv) as $imgPath) {
        //         $product->media()->create(['type'=>'color_image','url'=>trim($imgPath)]);
        //     }
        // }

        // Variants
        if ($hasValidVariants && !empty($variants)) {
            foreach ($variants as $variantData) {
                // Skip empty variants
                if (empty($variantData['color_id']) && empty($variantData['size_id']) &&
                    (empty($variantData['stock']) || $variantData['stock'] == 0)) {
                    continue;
                }

                // Create variant
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'color_id'   => $variantData['color_id'] ?? null,
                    'size_id'    => $variantData['size_id'] ?? null,
                    'sku'        => strtoupper(uniqid('V-')),
                    'price'      => $variantData['price'] ?? $product->price,
                    'stock'      => $variantData['stock'] ?? 0,
                ]);

                // Handle color images per variant
                if (!empty($variantData['color_images'])) {
                    $uploadedPaths = [];

                    foreach ($variantData['color_images'] as $index => $file) {
                        // Create a temporary request clone to match your uploadImage() function
                        $tempRequest = new Request();
                        $tempRequest->files->set('color_image', $file);

                        $path = uploadImage($tempRequest, 'color_image', 'storage/products/color_images');
                        if ($path) {
                            $uploadedPaths[] = $path;
                        }
                    }

                    // Save all uploaded paths to product_media
                    if (!empty($uploadedPaths)) {
                        foreach ($uploadedPaths as $imgPath) {
                            $product->media()->create([
                                'type'       => 'color_image',
                                'url'        => trim($imgPath),
                                'variant_id' => $variant->id,
                            ]);
                        }
                    }
                }
            }
        }


        return redirect()->route('vendor.products')->with('success', 'Product created successfully');
    }


    public function edit(Product $product)
    {
        if ($product->vendor_id !== auth('vendor')->id()) {
            abort(403);
        }

        $product->load(['media', 'media.variant', 'variants', 'translations']);

        // Get current locale
        $currentLocale = app()->getLocale();

        // Fetch main categories with translations for current locale
        $mainCategories = MainCategory::with(['translations' => function($query) use ($currentLocale) {
            $query->where('locale', $currentLocale);
        }])->get();

        // Fetch categories with translations for current locale
        $categories = Category::with(['translations' => function($query) use ($currentLocale) {
            $query->where('locale', $currentLocale);
        }])->get();

        $colors = ProductColor::all();
        $sizes = ProductSize::all();
        $brands = Brand::all();
        $zones = Zone::all();
        $consignment_types = ConsignmentType::all();
        $branches = VendorBranch::where('vendor_id', auth('vendor')->id())->get();

        return view('dashboard.vendor.products.edit', compact(
            'product',
            'mainCategories',
            'categories',
            'colors',
            'sizes',
            'brands',
            'zones',
            'consignment_types',
            'branches'
        ));
    }


    public function update(Request $request, Product $product)
    {
        if ($product->vendor_id !== auth('vendor')->id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'translations' => 'required|array',
            'translations.en.name' => 'required|string|max:255',
            'translations.en.description' => 'required|string',
            'translations.ar.name' => 'required|string|max:255',
            'translations.ar.description' => 'required|string',

            'price' => 'required|numeric|min:0',
            'brand_id' => 'nullable|exists:brands,id',
            'piece_type' => 'nullable|in:small,medium,large,xlarge',
            'pieces_per_package' => 'nullable|numeric|min:1',

            'main_category_id'   => 'required|exists:main_categories,id',
            'main_category_id_2' => 'nullable|exists:main_categories,id',
            'category_id'        => 'required|exists:categories,id',
            'category_id_2'      => 'nullable|exists:categories,id',
            'requires_delivery_otp' => 'nullable|boolean',


            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'videos.*' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
            'extra_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',

            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.color_id' => 'nullable|exists:product_colors,id',
            'variants.*.size_id' => 'nullable|exists:product_sizes,id',
            'variants.*.price'   => 'nullable|numeric|min:0',
            'variants.*.stock'   => 'nullable|integer|min:0',
            'variants.*.color_images' => 'nullable|array',

            // Extra details
            'features'            => 'nullable|string',
            'product_info'        => 'nullable|string',
            'usage_description'   => 'nullable|string',
            'parts_description'   => 'nullable|string',
            'material_description' => 'nullable|string',
            'dimensions'          => 'nullable|string',
            'weight'              => 'nullable|string',
            'volume'              => 'nullable|string',
            'available_sizes'     => 'nullable|string',
            'available_colors'    => 'nullable|array',
            'origin_country'      => 'nullable|string|max:255',
            'manufacturer'        => 'nullable|string|max:255',
            'model'               => 'nullable|string|max:255',
            'expiry_period'       => 'nullable|string|max:255',

            // Discounts & Shipping
            'discount_percentage'     => 'nullable|numeric|min:0|max:100',
            'discount_start'          => 'nullable|date|after_or_equal:today',
            'discount_end'            => 'nullable|date|after_or_equal:discount_start|after_or_equal:today',
            'free_shipping'           => 'nullable|string|in:0,available,price',
            'free_shipping_min_order' => 'nullable|numeric|min:0',
            'shipment_type'           => 'nullable|string|max:255',
            'shipment_description'    => 'nullable|string',
            'shipment_dimensions'     => 'nullable|string',
            'shipment_weight'         => 'nullable|string',

            // Storage & Delivery
            'storage_conditions' => 'nullable|array',
            'delivery_zones'     => 'nullable|array',
            'delivery_options'   => 'nullable|array',

            'is_active' => 'boolean',
            'stock'     => 'nullable|integer|min:0',

            'subcategories_level1' => 'nullable|string|max:255',
            'subcategory_level2'  => 'nullable|string|max:255',
            'auto_discount_end_date' => 'nullable|date',
            'free_shipping_price' => 'nullable|numeric|min:0',
            'package_length'      => 'nullable|numeric|min:0',
            'package_width'       => 'nullable|numeric|min:0',
            'package_height'      => 'nullable|numeric|min:0',
            'package_weight'      => 'nullable|numeric|min:0',

            // Return fields
            'is_returnable'       => 'nullable|boolean',
            'return_fee'           => 'nullable|numeric|min:0',
            'return_validity'      => 'nullable|numeric|min:0',
            'branch_id'           => 'nullable|exists:vendor_branches,id',

            'has_deposit' => 'nullable|boolean',
            'deposit_percentage' => 'nullable|required_if:has_deposit,1|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Remove old media if requested
        if ($request->has('remove_media_ids')) {
            $product->media()
                ->whereIn('id', $request->input('remove_media_ids'))
                ->get()
                ->each(function ($media) {
                    if (Storage::exists($media->url)) {
                        Storage::delete($media->url);
                    }
                    $media->delete();
                });
        }

        if ($request->has('storage_conditions')) {
            $validated['storage_conditions'] = json_encode($request->input('storage_conditions'), JSON_UNESCAPED_UNICODE);
        }

        $validated['is_active'] = $request->get('is_active');
        $validated['requires_delivery_otp'] = $request->get('requires_delivery_otp');
        $validated['is_returnable'] = $request->has('is_returnable');
        // $validated['name'] = $validated['translations']['en']['name'];
        // $validated['description'] = $validated['translations']['en']['description'];
        $validated['slug'] = Str::slug($validated['translations']['en']['name'] . ' ' . $product->sku);

        if (!empty($validated['has_deposit'])) {
            $price = $validated['price'];

            // Calculate deposit based on amount or percentage
            if (!empty($validated['deposit_amount'])) {
                $depositAmount = $validated['deposit_amount'];
            } elseif (!empty($validated['deposit_percentage'])) {
                $depositAmount = $price * ($validated['deposit_percentage'] / 100);
            } else {
                $depositAmount = 0;
            }

            // Calculate final price after deposit
            $validated['final_price_after_deposit'] = $price - $depositAmount;

            // Ensure final price is not negative
            if ($validated['final_price_after_deposit'] < 0) {
                $validated['final_price_after_deposit'] = 0;
            }
        } else {
            $validated['has_deposit'] = false;
            $validated['deposit_amount'] = null;
            $validated['deposit_percentage'] = null;
            $validated['final_price_after_deposit'] = null;
        }

        // Stock logic
        $variants = $validated['variants'] ?? [];
        $hasValidVariants = false;

        // Check if there are any valid variants (with color_id, size_id, or stock)
        if (!empty($variants)) {
            foreach ($variants as $variant) {
                if (!empty($variant['color_id']) || !empty($variant['size_id']) ||
                    (isset($variant['stock']) && $variant['stock'] > 0)) {
                    $hasValidVariants = true;
                    break;
                }
            }
        }

        if ($hasValidVariants) {
            // Calculate stock from variants if they exist
            $validated['stock'] = array_sum(array_column($variants, 'stock'));
        } else {
            // Use stock from input field if no variants
            $validated['stock'] = $validated['stock'] ?? $product->stock ?? 0;
        }

        // Discounted price
        if (!empty($validated['discount_percentage'])) {
            $validated['discounted_price'] = $validated['price'] - ($validated['price'] * $validated['discount_percentage'] / 100);
        }

        // Encode array fields
        foreach (
            [
                'features',
                'product_info',
                'available_sizes',
                'available_colors',
                'delivery_zones',
                'delivery_options',
                'storage_conditions'
            ] as $field
        ) {
            if ($request->has($field)) {
                $value = $request->input($field);
                $validated[$field] = is_array($value)
                    ? json_encode($value, JSON_UNESCAPED_UNICODE)
                    : $value;
            }
        }
        // Update product
        $product->update($validated);

        // Update translations
        foreach ($validated['translations'] as $locale => $translation) {
            $product->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name'   => $translation['name'],
                    'slug'   => Str::slug($translation['name'] . ' ' . $product->sku),
                    'short_description' => $translation['short_description'] ?? null,
                    'description'       => $translation['description'],
                ]
            );
        }

        // Media uploads
        $mediaTypes = [
            'images'       => 'image',
            'videos'       => 'video',
            'extra_images' => 'extra_image',
        ];

        foreach ($mediaTypes as $input => $type) {
            $csv = uploadImages($request, $input, "storage/products/$input");
            if ($csv) {
                foreach (explode(',', $csv) as $path) {
                    $product->media()->create(['type' => $type, 'url' => trim($path)]);
                }
            }
        }

        // Variants (update or create)
        if ($hasValidVariants && !empty($variants)) {
            // Get IDs of variants being kept/updated
            $keptVariantIds = [];

            foreach ($variants as $variantData) {
                // Skip empty variants (unless it's an update with ID)
                if (empty($variantData['id']) &&
                    empty($variantData['color_id']) && empty($variantData['size_id']) &&
                    (empty($variantData['stock']) || $variantData['stock'] == 0)) {
                    continue;
                }

                if (!empty($variantData['id'])) {
                    // Update existing variant
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant && $variant->product_id == $product->id) {
                        $variant->update([
                            'color_id' => $variantData['color_id'] ?? null,
                            'size_id'  => $variantData['size_id'] ?? null,
                            'price'    => $variantData['price'] ?? $product->price,
                            'stock'    => $variantData['stock'] ?? 0,
                        ]);
                        $keptVariantIds[] = $variant->id;
                    }
                } else {
                    // Create new variant
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'color_id'   => $variantData['color_id'] ?? null,
                        'size_id'    => $variantData['size_id'] ?? null,
                        'sku'        => strtoupper(uniqid('V-')),
                        'price'      => $variantData['price'] ?? $product->price,
                        'stock'      => $variantData['stock'] ?? 0,
                    ]);
                    $keptVariantIds[] = $variant->id;
                }

                // Handle variant color images
                if (!empty($variantData['color_images'])) {
                    $uploadedPaths = [];

                    foreach ($variantData['color_images'] as $index => $file) {
                        $tempRequest = new Request();
                        $tempRequest->files->set('color_image', $file);

                        $path = uploadImage($tempRequest, 'color_image', 'storage/products/color_images');
                        if ($path) {
                            $uploadedPaths[] = $path;
                        }
                    }

                    foreach ($uploadedPaths as $imgPath) {
                        $product->media()->create([
                            'type'       => 'color_image',
                            'url'        => trim($imgPath),
                            'variant_id' => $variant->id,
                        ]);
                    }
                }
            }

            // Delete variants that are NOT in the kept list
            // This handles variants that were removed from the form
            $product->variants()
                ->whereNotIn('id', $keptVariantIds)
                ->delete();

        } else {
            // If no valid variants, delete all existing variants
            $product->variants()->delete();
        }

        return redirect()
            ->route('vendor.products')
            ->with('success', 'Product updated successfully');
    }





    public function deleteImage(Product $product, ProductMedia $media)
    {
        if ($product->vendor_id !== auth('vendor')->id()) {
            abort(403);
        }

        if ($media->product_id !== $product->id) {
            abort(403);
        }
        // remove physical file from public path
        deleteImage($media->url);
        $media->delete();

        return redirect()
            ->back()
            ->with('success', 'Image deleted successfully');
    }

    public function toggleStatus(Product $product)
    {
        if ($product->vendor_id !== auth('vendor')->id()) {
            abort(403);
        }

        $product->update(['is_active' => !$product->is_active]);

        return redirect()
            ->back()
            ->with('success', 'Product status updated successfully');
    }
    public function reviews(Request $request)
    {
        $vendorId = auth('vendor')->id();

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'sort_by' => ['nullable', 'in:product_number,name,rating,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $reviewsQuery = ProductReview::query()
            ->whereHas('product', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->with([
                'product.media',
                'product.translations',
                'user',
            ]);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $reviewsQuery->where(function ($query) use ($search) {
                $query->where('comment', 'like', "%{$search}%")
                    ->orWhere('rating', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('product_number', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhereHas('translations', function ($translationQuery) use ($search) {
                                $translationQuery->where('name', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($sortBy === 'product_number') {
            $reviewsQuery->leftJoin('products as p', 'product_reviews.product_id', '=', 'p.id')
                ->select('product_reviews.*')
                ->orderBy('p.product_number', $sortDir);
        } elseif ($sortBy === 'name') {
            $locale = app()->getLocale();
            $reviewsQuery->leftJoin('products as p', 'product_reviews.product_id', '=', 'p.id')
                ->leftJoin('product_translations as pt', function ($join) use ($locale) {
                    $join->on('p.id', '=', 'pt.product_id')
                        ->where('pt.locale', '=', $locale);
                })
                ->select('product_reviews.*')
                ->orderBy('pt.name', $sortDir);
        } else {
            $reviewsQuery->orderBy("product_reviews.{$sortBy}", $sortDir);
        }

        $reviews = $reviewsQuery
            ->paginate(10)
            ->appends($request->query());

        return view('dashboard.vendor.products.review', compact('reviews'));
    }
}
