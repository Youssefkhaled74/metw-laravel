<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MainCategoryController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.main-categories.index')) {
            return view('dashboard.admin.no-permission');
        }

        $search = trim((string) $request->input('search'));
        $status = $request->input('status', 'all');
        $sort = $request->input('sort', 'id');
        $direction = strtolower((string) $request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['id', 'name', 'slug', 'created_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        $query = MainCategory::withoutGlobalScope('active')
            ->with('translations');

        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search) {
                $searchLike = '%' . $search . '%';

                $searchQuery->where('main_categories.name', 'like', $searchLike)
                    ->orWhere('main_categories.slug', 'like', $searchLike)
                    ->orWhereHas('translations', function ($translationQuery) use ($searchLike) {
                        $translationQuery->where('name', 'like', $searchLike)
                            ->orWhere('slug', 'like', $searchLike);
                    });
            });
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('is_active', $status === 'active');
        }

        $query->orderBy('main_categories.' . $sort, $direction);

        $mainCategories = $query->paginate(20)->appends($request->query());

        return view('dashboard.admin.settings.main-categories.index', compact('mainCategories', 'search', 'status', 'sort', 'direction'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.main-categories.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.main-categories.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.main-categories.store')) {
            return view('dashboard.admin.no-permission');
        }
        $validated = $request->validate([
            'translations.en.name' => 'required|string|max:255',
            'translations.en.slug' => 'nullable|string|max:255|unique:main_category_translations,slug',
            'translations.ar.name' => 'required|string|max:255',
            'translations.ar.slug' => 'nullable|string|max:255|unique:main_category_translations,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'is_active' => 'boolean',
        ]);

        $imagePath = uploadImage($request, 'image', 'storage/main-categories');
        $validated['name'] = $validated['translations']['en']['name'];
        $slug = Str::slug($validated['translations']['en']['name']);

        $validated['image']  = $imagePath;

        $validated['is_active'] = $request->has('is_active');
        $mainCategory = MainCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'image' => $imagePath,
            'is_active' => $validated['is_active'],
        ]);

        foreach ($validated['translations'] as $locale => $data) {
            $mainCategory->translations()->create([
                'locale' => $locale,
                'name'   => $data['name'],
                'slug'   => $data['slug'] ?? Str::slug($data['name']),
            ]);
        }
        return redirect()->route('admin.settings.main-categories.index')->with('success', 'Main category created successfully.');
    }

    public function edit(MainCategory $mainCategory)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.main-categories.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.main-categories.edit', compact('mainCategory'));
    }

    public function update(Request $request, MainCategory $mainCategory)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.main-categories.update')) {
            return view('dashboard.admin.no-permission');
        }
        $validated = $request->validate([
        'translations.en.name' => 'required|string|max:255',
        'translations.en.slug' => 'nullable|string|max:255|unique:main_category_translations,slug,'. optional($mainCategory->translation('en'))->id,
        'translations.ar.name' => 'required|string|max:255',
        'translations.ar.slug' => 'nullable|string|max:255|unique:main_category_translations,slug,'. optional($mainCategory->translation('ar'))->id,
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($mainCategory->image && File::exists(public_path($mainCategory->image))) {
                File::delete(public_path($mainCategory->image));
            }
            $imagePath = uploadImage($request, 'image', 'storage/main-categories');

            $validated['image'] = $imagePath;
        }

        $validated['name'] = $validated['translations']['en']['name'];
        $slug = Str::slug($validated['translations']['en']['slug']);

        $validated['is_active'] = $request->has('is_active');
        $mainCategory->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'image' => $imagePath,
            'is_active' => $validated['is_active'],
        ]);

        foreach ($validated['translations'] as $locale => $data) {
            $translation = $mainCategory->translations()->where('locale', $locale)->first();

            if ($translation) {
                $translation->update([
                    'name' => $data['name'],
                    'slug' => $data['slug'] ?? Str::slug($data['name']),
                ]);
            } else {
                $mainCategory->translations()->create([
                    'locale' => $locale,
                    'name' => $data['name'],
                    'slug' => $data['slug'] ?? Str::slug($data['name']),
                ]);
            }
        }
        return redirect()->route('admin.settings.main-categories.index')->with('success', 'Main category updated successfully.');
    }

    public function destroy(MainCategory $mainCategory)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.main-categories.destroy')) {
            return view('dashboard.admin.no-permission');
        }
         // حذف الصورة من التخزين إذا كانت موجودة
        if ($mainCategory->image) {
            Storage::disk('public')->delete($mainCategory->image);
        }

        $mainCategory->delete();
        return redirect()->route('admin.settings.main-categories.index')->with('success', 'Main category deleted successfully.');
    }

    public function toggleStatus(MainCategory $mainCategory)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.main-categories.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $mainCategory->update(['is_active' => !$mainCategory->is_active]);

        return redirect()
            ->route('admin.settings.main-categories.index')
            ->with('success', 'Main category status updated successfully.');
    }
}
