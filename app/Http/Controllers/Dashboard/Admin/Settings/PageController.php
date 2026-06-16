<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Enum\PageType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PageController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.pages.index')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $pagesQuery = Page::withoutGlobalScope('active');

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $pagesQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('title_ar', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('id', is_numeric($search) ? (int) $search : 0);
            });
        }

        $pages = $pagesQuery
            ->orderBy('type')
            ->orderByDesc('active_from')
            ->orderByDesc('id')
            ->get();

        $groupedPages = $pages->groupBy('type');
        $totalPages = $pages->count();
        $totalTypes = $groupedPages->count();

        return view('dashboard.admin.settings.pages.index', compact('groupedPages', 'totalPages', 'totalTypes'));
    }

    public function history(string $type)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.pages.index')) {
            return view('dashboard.admin.no-permission');
        }

        $allowedTypes = array_column(PageType::cases(), 'value');

        if (!in_array($type, $allowedTypes, true)) {
            abort(404);
        }

        $today = now()->toDateString();

        $historyPages = Page::withoutGlobalScope('active')
            ->where('type', $type)
            ->orderByDesc('active_from')
            ->orderByDesc('id')
            ->get();

        $isCurrentValid = function (Page $page) use ($today): bool {
            $fromOk = !$page->active_from || $page->active_from->toDateString() <= $today;
            $toOk = !$page->active_to || $page->active_to->toDateString() >= $today;

            return (bool) $page->is_active && $fromOk && $toOk;
        };

        $historyPages = $historyPages
            ->sort(function (Page $a, Page $b) use ($isCurrentValid) {
                $aCurrent = $isCurrentValid($a) ? 1 : 0;
                $bCurrent = $isCurrentValid($b) ? 1 : 0;

                if ($aCurrent !== $bCurrent) {
                    return $bCurrent <=> $aCurrent;
                }

                $aFrom = $a->active_from?->timestamp ?? PHP_INT_MIN;
                $bFrom = $b->active_from?->timestamp ?? PHP_INT_MIN;

                if ($aFrom !== $bFrom) {
                    return $bFrom <=> $aFrom;
                }

                return $b->id <=> $a->id;
            })
            ->values();

        $currentPageId = optional($historyPages->first(fn (Page $page) => $isCurrentValid($page)))->id;

        return view('dashboard.admin.settings.pages.history', compact('type', 'historyPages', 'currentPageId'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.pages.create')) {
            return view('dashboard.admin.no-permission');
        }
        $pageTypes = PageType::cases();
        return view('dashboard.admin.settings.pages.create', compact('pageTypes'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.pages.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_column(PageType::cases(), 'value')),
            'is_active' => 'boolean',
            'validity_mode' => 'nullable|in:custom_range,current_year,specific_year',
            'valid_year' => 'nullable|required_if:validity_mode,specific_year|integer|min:2000|max:2100',
            'active_from' => 'nullable|date',
            'active_to' => 'nullable|date|after_or_equal:active_from',
        ]);

        [$activeFrom, $activeTo] = $this->resolveActiveRange($request);

        $this->ensureNoTypeHistoryOverlap(
            $request->type,
            $activeFrom,
            $activeTo
        );

        Page::create([
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'slug' => $this->generateUniqueSlug($request->title),
            'content' => $request->content,
            'content_ar' => $request->content_ar,
            'type' => $request->type,
            'is_active' => $request->is_active ?? true,
            'active_from' => $activeFrom,
            'active_to' => $activeTo,
        ]);

        return redirect()
            ->route('admin.settings.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.pages.edit')) {
            return view('dashboard.admin.no-permission');
        }
        $pageTypes = PageType::cases();
        return view('dashboard.admin.settings.pages.edit', compact('page', 'pageTypes'));
    }

    public function update(Request $request, Page $page)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.pages.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_column(PageType::cases(), 'value')),
            'is_active' => 'boolean',
            'validity_mode' => 'nullable|in:custom_range,current_year,specific_year',
            'valid_year' => 'nullable|required_if:validity_mode,specific_year|integer|min:2000|max:2100',
            'active_from' => 'nullable|date',
            'active_to' => 'nullable|date|after_or_equal:active_from',
        ]);

        [$activeFrom, $activeTo] = $this->resolveActiveRange($request);

        $this->ensureNoTypeHistoryOverlap(
            $request->type,
            $activeFrom,
            $activeTo,
            $page->id
        );

        $page->update([
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'slug' => $this->generateUniqueSlug($request->title, $page->id),
            'content' => $request->content,
            'content_ar' => $request->content_ar,
            'type' => $request->type,
            'is_active' => $request->is_active ?? $page->is_active,
            'active_from' => $activeFrom,
            'active_to' => $activeTo,
        ]);

        return redirect()
            ->route('admin.settings.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.pages.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $page->delete();
        return redirect()
            ->route('admin.settings.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function toggleStatus(Page $page)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.pages.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $page->update(['is_active' => !$page->is_active]);
        return redirect()
            ->route('admin.settings.pages.index')
            ->with('success', 'Page status updated successfully.');
    }

    private function ensureNoTypeHistoryOverlap(string $type, ?string $activeFrom, ?string $activeTo, ?int $ignoreId = null): void
    {
        $historyRows = Page::withoutGlobalScope('active')
            ->where('type', $type)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->get(['id', 'active_from', 'active_to']);

        $newStart = $activeFrom ? strtotime($activeFrom) : PHP_INT_MIN;
        $newEnd = $activeTo ? strtotime($activeTo) : PHP_INT_MAX;

        foreach ($historyRows as $historyRow) {
            $rowStart = $historyRow->active_from ? strtotime((string) $historyRow->active_from) : PHP_INT_MIN;
            $rowEnd = $historyRow->active_to ? strtotime((string) $historyRow->active_to) : PHP_INT_MAX;

            if ($newStart <= $rowEnd && $rowStart <= $newEnd) {
                throw ValidationException::withMessages([
                    'active_from' => app()->getLocale() === 'ar'
                        ? 'الفترة الزمنية متعارضة مع سجل تاريخ آخر لنفس نوع الصفحة.'
                        : 'This date range conflicts with another history record for the selected page type.',
                ]);
            }
        }
    }

    private function resolveActiveRange(Request $request): array
    {
        $mode = $request->input('validity_mode', 'custom_range');

        if ($mode === 'current_year') {
            $year = (int) now()->year;
            return [sprintf('%d-01-01', $year), sprintf('%d-12-31', $year)];
        }

        if ($mode === 'specific_year') {
            $year = (int) $request->input('valid_year');
            return [sprintf('%d-01-01', $year), sprintf('%d-12-31', $year)];
        }

        return [$request->active_from, $request->active_to];
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (Page::withoutGlobalScope('active')
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
