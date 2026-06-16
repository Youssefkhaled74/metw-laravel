<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Product;

use App\Enum\ProductMediaType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\RecentView;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $page = $request->page ?? 1;
            $userId = null;
            if ($token = $request->bearerToken()) {
                $accessToken = PersonalAccessToken::findToken($token);
                $userId = $accessToken?->tokenable_id;
            }
            $query = Product::query()->latest()
                ->with(['media','variants.color','variants.media','variants.size', 'category', 'vendor', 'brand'])
                ->where('is_active', true);

            if ($userId) {
                $query->withCount(['favourite as is_favourite' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }]);
            }

            if ($request->filled('category_id')) {
                $query->byCategory($request->category_id);
            }

            if ($request->filled('vendor_id')) {
                $query->byVendor($request->vendor_id);
            }

            if ($request->filled('search')) {
                $query->search($request->search);
            }
            if ($request->filled('main_category_id')) {
                $query->byMainCategory($request->main_category_id);
            }
            if ($request->filled('brand_id')) {
                $query->byBrand($request->brand_id);
            }

            if ($request->filled('min_price') && $request->filled('max_price')) {
                $query->byPrice($request->min_price, $request->max_price);
            }

            // Handle filter parameter
            if ($request->filled('filter')) {
                $filter = $request->filter;

                switch ($filter) {
                    case 'similar_to_purchases':
                        if ($userId) {
                            $query->similarToPurchases($userId);
                        } else {
                            // If no user (guest), show top rated products as fallback
                            $query->topRated();
                        }
                        break;

                    case 'recommended_by_buyers':
                        if ($userId) {
                            $query->recommendedByBuyers($userId);
                        } else {
                            // If no user (guest), show top rated products
                            $query->topRated();
                        }
                        break;

                    case 'previous_purchases':
                        if ($userId) {
                            $query->previousPurchases($userId);
                        } else {
                            // If no user (guest), show popular products instead
                            $query->where('is_active', true)
                                ->orderBy('sold_count', 'desc')
                                ->orderBy('rating', 'desc');
                        }
                        break;

                    case 'recommended_deals':
                        $query->recommendedDeals($userId);
                        break;

                    case 'top_rated':
                        $query->topRated();
                        break;

                    case 'new_arrivals':
                        $query->newArrivals();
                        break;

                    case 'recent_views':
                        if ($userId) {
                            $query->recentViews($userId);
                        } else {
                            // If no user (guest), show top rated products
                            $query->topRated();
                        }
                        break;

                    case 'similar_to_recent_views':
                        if ($userId) {
                            $query->similarToRecentViews($userId);
                        } else {
                            // If no user (guest), show top rated products
                            $query->topRated();
                        }
                        break;
                    default:
                        // No additional filtering for unknown filters
                        break;
                }
            }


            $payload = paginate($query, ProductResource::class, $limit, $page, $request->all());


            return responseJson(true, trans('messages.Products fetched successfully'), $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function show($id)
    {
        try {
            $userId = null;

            // Safely get user ID if authenticated
            if (request()->bearerToken()) {
                try {
                    $accessToken = PersonalAccessToken::findToken(request()->bearerToken());
                    $userId = $accessToken?->tokenable_id;
                } catch (\Exception $e) {
                    // Invalid token, continue as guest
                    $userId = null;
                }
            }

            $productQuery = Product::with([
                'media',
                'maincategory',
                'category',
                'vendor',
                'brand',
                'variants.color',
                'variants.media',
                'variants.size',
                'relatedProducts.media',
                'reviews' => function ($q) {
                    $q->latest()->limit(3)->with('user');
                }
            ]);
            if ($userId) {
                $productQuery->withCount(['favourite as is_favourite' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }]);

            }
            $product = $productQuery->findOrFail($id);

            if ($userId) {
                RecentView::updateOrCreate(
                    [
                        'user_id'    => $userId,
                        'product_id' => $id,
                    ],
                    ['updated_at' => now()]
                );
            }


            return responseJson(true, trans('messages.Product fetched successfully'), new ProductResource($product));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['sku'] = random_int(100000, 999999);

            $validatedData['slug'] = str()->slug($validatedData['name'] . '-' . $validatedData['sku']);

            $product = Product::create($validatedData);

            if ($request->has('translations')) {
                foreach ($request->translations as $locale => $data) {
                    $product->translations()->create([
                        'locale' => $locale,
                        'name'   => $data['name'],
                        'slug'   => str()->slug($data['name'] . '-' . $product->sku),
                        'short_description' => $data['short_description'] ?? null,
                        'description'       => $data['description'] ?? null,
                    ]);
                }
            }

            if ($request->has('media')) {
                $mediaData = [];

                foreach ($request->media as $index => $media) {
                    // خد الـ file بالـ index ده
                    $file = $request->file("media.$index.file");

                    if ($file) {
                        if ($media['type'] == ProductMediaType::IMAGE->value) {
                            $url = uploadImage($request, "media.$index.file", 'storage/products');
                        } else {
                            $url = uploadVideo($request, "media.$index.file", 'storage/products');
                        }

                        $mediaData[] = [
                            'type' => $media['type'],
                            'url' => $url,
                            'position' => $media['position'] ?? 0,
                        ];
                    }
                }

                // احفظ mediaData
                $product->media()->createMany($mediaData);
            }


            if ($request->has('variants')) {
                $variantsData = collect($request->variants)->map(function ($variant) {
                    return array_merge($variant, [
                        'sku' => random_int(100000, 999999),
                    ]);
                })->toArray();

                $product->variants()->createMany($variantsData);
            }

            if ($request->has('related_products')) {
                $product->relatedProducts()->sync($request->related_products);
            }
            return responseJson(true, trans('messages.Product created successfully'), new ProductResource($product));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function update(UpdateProductRequest $request, $productId)
    {
        try {
            $validatedData = $request->validated();

            $product = Product::findOrFail($productId);

            // slug يتعدل لو الاسم اتغير
            if (isset($validatedData['name'])) {
                $validatedData['slug'] = str()->slug($validatedData['name'] . '-' . $product->sku);
            }

            $product->update($validatedData);

            /**
             * Handle Media
             */
            if ($request->has('media')) {
                // امسح القديم
                $product->media()->delete();

                $mediaData = [];
                foreach ($request->media as $index => $media) {
                    $file = $request->file("media.$index.file");

                    if ($file) {
                        if ($media['type'] == ProductMediaType::IMAGE->value) {
                            $url = uploadImage($request, "media.$index.file", 'storage/products');
                        } else {
                            $url = uploadVideo($request, "media.$index.file", 'storage/products');
                        }

                        $mediaData[] = [
                            'type' => $media['type'],
                            'url' => $url,
                            'position' => $media['position'] ?? 0,
                        ];
                    }
                }

                if (!empty($mediaData)) {
                    $product->media()->createMany($mediaData);
                }
            }

            /**
             * Handle Variants
             */
            if ($request->has('variants')) {
                // امسح القديم
                $product->variants()->delete();

                $variantsData = collect($request->variants)->map(function ($variant) {
                    return array_merge($variant, [
                        'sku' => random_int(100000, 999999),
                    ]);
                })->toArray();

                if (!empty($variantsData)) {
                    $product->variants()->createMany($variantsData);
                }
            }

            /**
             * Handle Related Products
             */
            if ($request->has('related_products')) {
                $product->relatedProducts()->sync($request->related_products);
            }

            return responseJson(true, trans('messages.Product updated successfully'), new ProductResource($product->load(['media', 'variants', 'relatedProducts','brand'])));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
