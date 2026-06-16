<?php

namespace App\Http\Controllers\Api\V1\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Resources\{
    MainCategoryResource,
    CategoryResource,
    BannarResource,
    VendorResource,
    BrandResource,
    ProductResource
};
use App\Models\{
    MainCategory,
    Category,
    Bannar,
    Vendor,
    Brand,
    Product
};

class HomeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $userId = null;

            if ($token = $request->bearerToken()) {
                $accessToken = PersonalAccessToken::findToken($token);
                $userId = $accessToken?->tokenable_id;
            }

            // 🏷️ Main Categories
            $categories = MainCategory::active()->take(10)->get();

            // 🏷️ Home Categories with top 4 products
            $homeCategories = Category::query()
                ->with([
                    'products' => function ($q) {
                        $q->with('media')
                            ->where('is_active', true)
                            ->orderByDesc('sold_count');
                    }
                ])
                ->active()
                ->take(10)
                ->get()
                ->each(function ($category) {
                    // خلى كل كاتيجورى يظهر 4 منتجات بس
                    $category->setRelation('products', $category->products->take(4));
                });


            // 🖼️ Banners
            $banners = Bannar::active()->get();

            // 🏪 Vendors
            $vendors = Vendor::where('is_active', true)
                ->take(10)
                ->get();

            // 🧢 Brands
            $brands = Brand::active()
                ->take(10)
                ->get();

            // 🛒 Products (with filters)
            $filters = [
                'recent_views',
                'similar_to_recent_views',
                'similar_to_purchases',
                'recommended_by_buyers',
                'previous_purchases',
                'recommended_deals',
                'top_rated',
                'new_arrivals',
            ];

            $productsData = [];

            foreach ($filters as $filter) {
                $query = Product::query()->latest()
                    ->with(['media', 'category', 'vendor', 'brand'])
                    ->where('is_active', true);

                switch ($filter) {
                    case 'similar_to_purchases':
                        if ($userId) $query->similarToPurchases($userId);
                        else $query= null;
                        break;

                    case 'recommended_by_buyers':
                        if ($userId) $query->recommendedByBuyers($userId);
                        else $query->topRated();
                        break;

                    case 'previous_purchases':
                        if ($userId) $query->previousPurchases($userId);
                        else $query= null;
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
                        if ($userId) $query->recentViews($userId);
                        else $query = null;
                        break;

                    case 'similar_to_recent_views':
                        if ($userId) $query->similarToRecentViews($userId);
                        else $query = null;
                        break;
                }

                if ($query) {
                    $productsData[$filter] = ProductResource::collection(
                        $query->take(4)->get()
                    );
                } else {
                    $productsData[$filter] = []; // Collection فاضية
                }
            }

            $payload = [
                'main_categories'      => MainCategoryResource::collection($categories),
                'banners'         => BannarResource::collection($banners),
                'vendors'         => VendorResource::collection($vendors),
                'brands'          => BrandResource::collection($brands),
                'products'        => $productsData,
                'categories' => CategoryResource::collection($homeCategories),
            ];

            return responseJson(true, 'Home data fetched successfully', $payload);

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function search(Request $request)
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
            ->with(['media', 'category', 'vendor', 'brand', 'maincategory'])
            ->where('is_active', true)
            ->latest();

            // ✅ تحديد إذا كان المستخدم عامل Favourite ولا لأ
            if ($userId) {
                $query->withCount(['favourite as is_favourite' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }]);
            }

            // ✅ البحث بالكلمة
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            // ✅ أكثر من كاتيجوري
            if ($request->filled('category_ids')) {
                $categoryIds = is_array($request->category_ids)
                    ? $request->category_ids
                    : explode(',', $request->category_ids);

                $query->whereIn('category_id', $categoryIds);
            }

            // ✅ أكثر من Vendor
            if ($request->filled('vendor_ids')) {
                $vendorIds = is_array($request->vendor_ids)
                    ? $request->vendor_ids
                    : explode(',', $request->vendor_ids);

                $query->whereIn('vendor_id', $vendorIds);
            }

            // ✅ أكثر من Brand
            if ($request->filled('brand_ids')) {
                $brandIds = is_array($request->brand_ids)
                    ? $request->brand_ids
                    : explode(',', $request->brand_ids);

                $query->whereIn('brand_id', $brandIds);
            }

            // ✅ Main Category
            if ($request->filled('main_category_id')) {
                $query->byMainCategory($request->main_category_id);
            }

            // ✅ نطاق السعر
            if ($request->filled('min_price') && $request->filled('max_price')) {
                $query->byPrice($request->min_price, $request->max_price);
            }

            $payload = paginate($query, ProductResource::class, $limit, $page, $request->all());

            return responseJson(true, 'Products fetched successfully', $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function priceRange()
    {
        try {
            $min = Product::where('is_active', true)->min('price');
            $max = Product::where('is_active', true)->max('price');

            return responseJson(true, 'Price range fetched successfully', [
                'min_price' => (float) $min,
                'max_price' => (float) $max,
            ]);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }

    }

}
