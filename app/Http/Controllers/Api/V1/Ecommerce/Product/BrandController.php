<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\MainCategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\MainCategory;
use App\Models\Product;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit  = $request->limit ?? 10;
            $page   = $request->page ?? 1;
            $search = $request->search;

            $brands = Brand::query()->active();

            // 🔍 Apply search if provided
            if (!empty($search)) {
                $brands->where(function ($query) use ($search) {
                    $query->where('name_en', 'LIKE', "%{$search}%")
                        ->orWhere('name_ar', 'LIKE', "%{$search}%");
                });
            }

            $payload = paginate($brands, BrandResource::class, $limit, $page);

            return responseJson(true, trans('messages.brands fetched successfully'), $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    public function show($id)
    {
        try{
            $brand = Brand::findOrFail($id);
            return responseJson(true,trans('messages.brand fetched successfully'),new BrandResource($brand));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }


    public function brandProducts($id, Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $page  = $request->page ?? 1;

            $brand = Brand::active()->findOrFail($id);

            $products = Product::where('brand_id', $brand->id)
                ->with(['media', 'variants', 'category.translations', 'vendor', 'brand'])
                ->where('is_active', true)
                ->latest()
                ->paginate($limit, ['*'], 'page', $page);

            $payload = [
                'brand'    => new BrandResource($brand),
                'products' => ProductResource::collection($products),
                'meta'     => [
                    'total'        => $products->total(),
                    'per_page'     => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page'    => $products->lastPage(),
                ]
            ];

            return responseJson(true, trans('messages.products fetched successfully'), $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

}
