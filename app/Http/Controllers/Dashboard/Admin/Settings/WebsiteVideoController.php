<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\WebsiteVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WebsiteVideoController extends Controller
{
    public function index()
    {
        $videos = WebsiteVideo::withoutGlobalScope('active')
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->paginate(10);

        return view('dashboard.admin.settings.website-videos.index', compact('videos'));
    }

    public function create()
    {
        return view('dashboard.admin.settings.website-videos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo', 'max:51200'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        File::ensureDirectoryExists(public_path('storage/website-videos'));
        $videoPath = uploadVideo($request, 'video', 'storage/website-videos');
        $thumbnailPath = uploadImage($request, 'thumbnail', 'storage/website-videos');

        WebsiteVideo::create([
            'title' => $validated['title'],
            'title_ar' => $validated['title_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
            'video_path' => $videoPath,
            'thumbnail' => $thumbnailPath,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.website-videos.index')
            ->with('success', 'تم حفظ فيديو الموقع بنجاح.');
    }

    public function edit(WebsiteVideo $websiteVideo)
    {
        return view('dashboard.admin.settings.website-videos.edit', compact('websiteVideo'));
    }

    public function update(Request $request, WebsiteVideo $websiteVideo)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo', 'max:51200'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        File::ensureDirectoryExists(public_path('storage/website-videos'));
        $data = [
            'title' => $validated['title'],
            'title_ar' => $validated['title_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('video')) {
            if ($websiteVideo->video_path && File::exists(public_path($websiteVideo->video_path))) {
                File::delete(public_path($websiteVideo->video_path));
            }
            $data['video_path'] = uploadVideo($request, 'video', 'storage/website-videos');
        }

        if ($request->hasFile('thumbnail')) {
            if ($websiteVideo->thumbnail && File::exists(public_path($websiteVideo->thumbnail))) {
                File::delete(public_path($websiteVideo->thumbnail));
            }
            $data['thumbnail'] = uploadImage($request, 'thumbnail', 'storage/website-videos');
        }

        $websiteVideo->update($data);

        return redirect()
            ->route('admin.settings.website-videos.index')
            ->with('success', 'تم تحديث فيديو الموقع بنجاح.');
    }

    public function destroy(WebsiteVideo $websiteVideo)
    {
        if ($websiteVideo->video_path && File::exists(public_path($websiteVideo->video_path))) {
            File::delete(public_path($websiteVideo->video_path));
        }

        if ($websiteVideo->thumbnail && File::exists(public_path($websiteVideo->thumbnail))) {
            File::delete(public_path($websiteVideo->thumbnail));
        }

        $websiteVideo->delete();

        return redirect()
            ->route('admin.settings.website-videos.index')
            ->with('success', 'تم حذف فيديو الموقع بنجاح.');
    }

    public function toggleStatus(WebsiteVideo $websiteVideo)
    {
        $websiteVideo->update(['is_active' => ! $websiteVideo->is_active]);

        return redirect()
            ->route('admin.settings.website-videos.index')
            ->with('success', 'تم تحديث حالة الفيديو بنجاح.');
    }
}
