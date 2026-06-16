<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\MainCategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\MainCategory;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $page  = $request->page ?? 1;
            $search = $request->search;

            $categories = MainCategory::query()->active();

            // 🔍 Apply search if provided
            if (!empty($search)) {
                $categories->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->orWhereHas('translations', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            $payload = paginate($categories, MainCategoryResource::class, $limit, $page);

            return responseJson(true, trans('messages.Categories fetched successfully'), $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    public function subCategories(Request $request, $mainCategoryId)
    {
        try {
            $limit  = $request->limit ?? 10;
            $page   = $request->page ?? 1;
            $search = $request->search;

            $subCategories = Category::query()
                ->active()
                ->where('main_category_id', $mainCategoryId);

            // 🔍 Apply search if provided
            if (!empty($search)) {
                $subCategories->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->orWhereHas('translations', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            $payload = paginate($subCategories, CategoryResource::class, $limit, $page);

            return responseJson(true, 'Sub categories fetched successfully', $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    public function mainCategories(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $page  = $request->page ?? 1;

            // if category_id is sent → fetch single main category with categories + 4 products
            if ($request->has('category_id')) {
                $mainCategory = MainCategory::query()
                    ->with([
                        'categories' => function ($q) {
                            $q->active()->with([
                                'products' => function ($q2) {
                                    $q2->with('media')->take(4);
                                }
                            ]);
                        }
                    ])
                    ->findOrFail($request->category_id);

                return responseJson(true, trans('messages.Main category fetched successfully'), new MainCategoryResource($mainCategory));
            }

            // else → fetch all main categories paginated
            $mainCategories = MainCategory::query()->paginate($limit, ['*'], 'page', $page);

            return responseJson(true, trans('messages.Main categories fetched successfully'), data: MainCategoryResource::collection($mainCategories));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    public function homeCategories(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $page = $request->page ?? 1;

            $categories = Category::query()
                ->active()
                ->whereHas('products', function ($q) {
                    // optionally filter products if needed, e.g., only active products
                    $q->with('media');
                })
                ->with(['products' => function ($q) {
                    $q->with('media')
                    ->orderByDesc('sold_count')
                    ->take(4);
                }]);

            // هنا انت عامل فانكشن paginate مخصصة
            $payload = paginate($categories, CategoryResource::class, $limit, $page);

            return responseJson(true, trans('messages.Categories fetched successfully'), $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try{
            $validatedData = $request->validated();

            $validatedData['slug'] = str()->slug($validatedData['name']);

            if  ($request->hasFile('image')){
                $validatedData['image'] = uploadImage($request,'image','storage/categories');
            }

            $category = Category::create($validatedData);
            return responseJson(true,trans('messages.Category created successfully'),new CategoryResource($category));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($categoryId)
    {
        try{
            $category = Category::findOrFail($categoryId);
            return responseJson(true,trans('messages.Category fetched successfully'),new CategoryResource($category));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $categoryId)
    {
        try{
            $validatedData = $request->validated();

            $category = Category::findOrFail($categoryId);

            if  ($request->hasFile('image')){
                $validatedData['image'] = uploadImage($request,'image','storage/categories');
            }

            $category->update($validatedData);
            return responseJson(true,trans('messages.Category updated successfully'),new CategoryResource($category));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($categoryId)
    {
        try{
            $category = Category::findOrFail($categoryId);
            $category->delete();
            return responseJson(true,trans('messages.Category deleted successfully'),new CategoryResource($category));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
}
