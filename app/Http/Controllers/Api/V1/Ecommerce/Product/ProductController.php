<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\RecentView;
use App\Services\ProductWriteService;
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

            $query = Product::query()
                ->latest()
                ->with([
                    'media',
                    'variants.color',
                    'variants.media',
                    'variants.size',
                    'category',
                    'vendor',
                    'brand',
                    'shippingProfile',
                    'returnPolicy',
                    'shippingFeePolicy',
                ])
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

            if ($request->filled('filter')) {
                switch ($request->filter) {
                    case 'similar_to_purchases':
                        $userId ? $query->similarToPurchases($userId) : $query->topRated();
                        break;
                    case 'recommended_by_buyers':
                        $userId ? $query->recommendedByBuyers($userId) : $query->topRated();
                        break;
                    case 'previous_purchases':
                        if ($userId) {
                            $query->previousPurchases($userId);
                        } else {
                            $query->where('is_active', true)->orderBy('sold_count', 'desc')->orderBy('rating', 'desc');
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
                        $userId ? $query->recentViews($userId) : $query->topRated();
                        break;
                    case 'similar_to_recent_views':
                        $userId ? $query->similarToRecentViews($userId) : $query->topRated();
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

            if (request()->bearerToken()) {
                try {
                    $accessToken = PersonalAccessToken::findToken(request()->bearerToken());
                    $userId = $accessToken?->tokenable_id;
                } catch (\Throwable) {
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
                'shippingProfile',
                'returnPolicy',
                'shippingFeePolicy',
                'reviews' => function ($q) {
                    $q->latest()->limit(3)->with('user');
                },
            ]);

            if ($userId) {
                $productQuery->withCount(['favourite as is_favourite' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }]);
            }

            $product = $productQuery->findOrFail($id);

            if ($userId) {
                RecentView::updateOrCreate(
                    ['user_id' => $userId, 'product_id' => $id],
                    ['updated_at' => now()]
                );
            }

            return responseJson(true, trans('messages.Product fetched successfully'), new ProductResource($product));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function store(StoreProductRequest $request, ProductWriteService $productWriteService)
    {
        try {
            $product = $productWriteService->create($request->validated(), $request);

            return responseJson(true, trans('messages.Product created successfully'), new ProductResource($product));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function update(UpdateProductRequest $request, $productId, ProductWriteService $productWriteService)
    {
        try {
            $product = Product::findOrFail($productId);
            $product = $productWriteService->update($product, $request->validated(), $request);

            return responseJson(true, trans('messages.Product updated successfully'), new ProductResource($product));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
