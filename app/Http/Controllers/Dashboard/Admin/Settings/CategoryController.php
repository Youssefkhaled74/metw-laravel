<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.categories.index')) {
            return view('dashboard.admin.no-permission');
        }

        $search = trim((string) $request->input('search'));
        $status = $request->input('status', 'all');
        $mainCategoryId = $request->input('main_category_id', 'all');
        $sort = $request->input('sort', 'id');
        $direction = strtolower((string) $request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['id', 'name', 'slug', 'main_category', 'created_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        $query = Category::withoutGlobalScope('active')
            ->select('categories.*')
            ->with(['translations', 'mainCategory.translations']);

        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search) {
                $searchLike = '%' . $search . '%';

                $searchQuery->where('categories.name', 'like', $searchLike)
                    ->orWhere('categories.slug', 'like', $searchLike)
                    ->orWhereHas('translations', function ($translationQuery) use ($searchLike) {
                        $translationQuery->where('name', 'like', $searchLike)
                            ->orWhere('slug', 'like', $searchLike);
                    })
                    ->orWhereHas('mainCategory', function ($mainCategoryQuery) use ($searchLike) {
                        $mainCategoryQuery->where('name', 'like', $searchLike)
                            ->orWhereHas('translations', function ($translationQuery) use ($searchLike) {
                                $translationQuery->where('name', 'like', $searchLike);
                            });
                    });
            });
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('is_active', $status === 'active');
        }

        if ($mainCategoryId !== 'all' && is_numeric($mainCategoryId)) {
            $query->where('main_category_id', $mainCategoryId);
        }

        if ($sort === 'main_category') {
            $query->leftJoin('main_categories', 'main_categories.id', '=', 'categories.main_category_id')
                ->orderBy('main_categories.name', $direction)
                ->orderBy('categories.id', 'desc');
        } else {
            $query->orderBy('categories.' . $sort, $direction);
        }

        $categories = $query->paginate(10)->appends($request->query());

        $mainCategories = MainCategory::withoutGlobalScope('active')
            ->with('translations')
            ->orderBy('name')
            ->get();

        return view('dashboard.admin.settings.categories.index', compact('categories', 'mainCategories', 'search', 'status', 'mainCategoryId', 'sort', 'direction'));
    }

    public function create()
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.categories.create')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $mainCategories = MainCategory::with('translations')->active()->get();

        return view('dashboard.admin.settings.categories.create', compact('mainCategories'));
    }


    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.categories.store')) {
            return view('dashboard.admin.no-permission');
        }
        $validated = $request->validate([
            'translations.en.name' => 'required|string|max:255',
            'translations.ar.name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'main_category_id' => 'required|exists:main_categories,id',
            'is_active' => 'boolean',
            'type' => 'required|in:piece,weight,weight_size',

        ]);

        $slug = Str::slug($validated['translations']['en']['name']);

        $validated['name'] = $validated['translations']['en']['name'];

        // استدعاء الفنكشن اللي عندك
        $imagePath = uploadImage($request, 'image', 'storage/categories');

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'image' => $imagePath,
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active', true),
            'main_category_id' => $validated['main_category_id'],
        ]);

            foreach ($validated['translations'] as $locale => $data) {
            $category->translations()->create([
                'locale' => $locale,
                'name'   => $data['name'],
                'slug'   => Str::slug($data['name']),
            ]);
        }

        return redirect()
            ->route('admin.settings.categories.index')
            ->with('success', 'Category created successfully.');
    }


    public function edit(Category $category)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.categories.edit')) {
            return view('dashboard.admin.no-permission');
        }
        $mainCategories = MainCategory::all();
        return view('dashboard.admin.settings.categories.edit', compact('category', 'mainCategories'));
    }



    public function update(Request $request, Category $category)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.categories.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'main_category_id' => 'required|exists:main_categories,id',
            'is_active' => 'boolean',
            'type' => 'required|in:piece,weight,weight_size',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->boolean('is_active', true),
            'main_category_id' => $request->main_category_id,
            'type' => $request->type,
        ];

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة (لو موجودة)
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $imagePath = uploadImage($request, 'image', 'storage/categories');

            // رفع الصورة الجديدة بالفنكشن
            $data['image'] = $imagePath;
        }

        $category->update($data);

        return redirect()
            ->route('admin.settings.categories.index')
            ->with('success', 'Category updated successfully.');
    }


    public function destroy(Category $category)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.categories.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        // Delete the image file
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.settings.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleStatus(Category $category)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.categories.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $category->update(['is_active' => !$category->is_active]);

        return redirect()
            ->route('admin.settings.categories.index')
            ->with('success', 'Category status updated successfully.');
    }
}
