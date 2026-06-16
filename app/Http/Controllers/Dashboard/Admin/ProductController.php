<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\EcommerceOrder;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public $locale;

    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $this->locale = app()->getLocale();
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.products')) {
            return view('dashboard.admin.no-permission');
        }

        $filters = $request->only([
            'search',
            'brand_id',
            'vendor_id',
            'main_category_id',
            'category_id',
        ]);

        // Get current locale for translations
        $currentLocale = app()->getLocale();

        // Eager load translations for the current locale
        $productsQuery = Product::withoutGlobalScope('active')->with([
            'vendor',
            'category',
            'media',
            'translations' => function($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            }
        ])->latest();

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $productsQuery->where(function ($query) use ($searchTerm, $currentLocale) {
                // Search in current locale translations
                $query->whereHas('translations', function ($translationQuery) use ($searchTerm, $currentLocale) {
                    $translationQuery->where('locale', $currentLocale)
                        ->where(function($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('description', 'like', "%{$searchTerm}%")
                            ->orWhere('short_description', 'like', "%{$searchTerm}%");
                        });
                })
                // Also search in other locales as fallback
                ->orWhereHas('translations', function ($translationQuery) use ($searchTerm) {
                    $translationQuery->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhere('short_description', 'like', "%{$searchTerm}%");
                })
                // Search in original product fields
                ->orWhere('name', 'like', "%{$searchTerm}%")
                ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        if (!empty($filters['brand_id'])) {
            $productsQuery->byBrand($filters['brand_id']);
        }

        if (!empty($filters['vendor_id'])) {
            $productsQuery->byVendor($filters['vendor_id']);
        }

        if (!empty($filters['main_category_id'])) {
            $productsQuery->byMainCategory($filters['main_category_id']);
        }

        if (!empty($filters['category_id'])) {
            $productsQuery->byCategory($filters['category_id']);
        }

        $products = $productsQuery
            ->paginate(20)
            ->appends($request->query());

        // Get brands with current locale ordering
        $brands = Brand::orderBy('name_'.$this->locale)->get();
        $vendors = Vendor::orderBy('name')->get();
        $mainCategories = MainCategory::orderBy('name')->get();
        $categories = Category::when(!empty($filters['main_category_id']), function ($query) use ($filters) {
            $query->where('main_category_id', $filters['main_category_id']);
        })->orderBy('name')->get();

        return view('dashboard.admin.products', compact('products', 'brands', 'vendors', 'mainCategories', 'categories', 'filters'));
    }

    public function show(Product $product)
    {
        $product = $product->id;
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.products.show')) {
            return view('dashboard.admin.no-permission');
        }
        $product = Product::withoutGlobalScope('active')->with([
            'vendor',
            'brand',
            'category',
            'maincategory',
            'branch',
            'translations',
            'images',
            'videos',
            'variants',
        ])
            ->findOrFail($product);

        $orders = EcommerceOrder::whereHas('items', function ($query) use ($product) {
            $query->where('product_id', $product);
        })
            ->with(['user', 'items' => function ($query) use ($product) {
                $query->where('product_id', $product);
            }])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.admin.product-details', compact('product', 'orders'));
    }

    public function toggleStatus($id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.products.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Product {$status} successfully.");
    }
}
