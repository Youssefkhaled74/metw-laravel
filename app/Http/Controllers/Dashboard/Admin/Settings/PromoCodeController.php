<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PromoCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        try {
            if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.promo_codes.index')) {
                return view('dashboard.admin.no-permission');
            }

            $validated = request()->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'valid_from' => ['nullable', 'date'],
                'valid_to' => ['nullable', 'date'],
                'status' => ['nullable', 'in:all,active,inactive'],
                'type' => ['nullable', 'in:all,shipment,ecommerce'],
                'discount_type' => ['nullable', 'in:all,percentage,fixed'],
                'sort_by' => ['nullable', 'in:code,discount_value,valid_from,valid_to,max_uses,user_max_uses,uses,created_at'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDir = $validated['sort_dir'] ?? 'desc';

            $promoCodesQuery = PromoCode::query();

            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $promoCodesQuery->where(function ($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('discount_type', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            }

            if (!empty($validated['valid_from'])) {
                $promoCodesQuery->whereDate('valid_from', '>=', $validated['valid_from']);
            }

            if (!empty($validated['valid_to'])) {
                $promoCodesQuery->whereDate('valid_to', '<=', $validated['valid_to']);
            }

            if (!empty($validated['status']) && $validated['status'] !== 'all') {
                $promoCodesQuery->where('is_active', $validated['status'] === 'active');
            }

            if (!empty($validated['type']) && $validated['type'] !== 'all') {
                $promoCodesQuery->where('type', $validated['type']);
            }

            if (!empty($validated['discount_type']) && $validated['discount_type'] !== 'all') {
                $promoCodesQuery->where('discount_type', $validated['discount_type']);
            }

            $promo_codes = $promoCodesQuery
                ->orderBy($sortBy, $sortDir)
                ->paginate(perPage: 10)
                ->appends(request()->query());

            return view('dashboard.admin.settings.promo_codes.index', compact('promo_codes', 'sortBy', 'sortDir'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.promo_codes.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.promo_codes.create');
    }


    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.promo_codes.create')) {
            return view('dashboard.admin.no-permission');
        }
        $validated = $request->validate([
            'code'           => 'required|string|max:50|unique:promo_codes,code',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'valid_from'     => 'required|date',
            'valid_to'       => 'required|date|after_or_equal:valid_from',
            'max_uses'       => 'nullable|integer|min:1',
            'user_max_uses'  => 'nullable|integer|min:1',
            'is_active'      => 'boolean',
            'type'           => 'required|in:shipment,ecommerce',
        ]);

        // Default values
        $validated['uses'] = 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        PromoCode::create($validated);

        return redirect()
            ->route('admin.settings.promo_codes.index')
            ->with('success', 'Promo Code created successfully.');
    }


    public function edit(PromoCode $promo_code)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.promo_codes.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.promo_codes.edit', compact('promo_code'));
    }



    public function update(Request $request, PromoCode $promo_code)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.promo_codes.update')) {
            return view('dashboard.admin.no-permission');
        }
        $validated = $request->validate([
            'code'           => 'required|string|max:50|unique:promo_codes,code,' . $promo_code->id,
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'valid_from'     => 'required|date',
            'valid_to'       => 'required|date|after_or_equal:valid_from',
            'max_uses'       => 'nullable|integer|min:1',
            'user_max_uses'  => 'nullable|integer|min:1',
            'is_active'      => 'boolean',
            'type'           => 'required|in:shipment,ecommerce',
        ]);

        // Keep current uses (don’t reset to 0)
        $validated['uses'] = $promo_code->uses ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        $promo_code->update($validated);

        return redirect()
            ->route('admin.settings.promo_codes.index')
            ->with('success', 'Promo Code updated successfully.');
    }


    public function destroy(PromoCode $promo_code)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.promo_codes.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $promo_code->delete();

        return redirect()
            ->route('admin.settings.promo_codes.index')
            ->with('success', 'Promo Code deleted successfully.');
    }

    public function toggleStatus(PromoCode $promo_code)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.promo_codes.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $promo_code->update(['is_active' => !$promo_code->is_active]);

        return redirect()
            ->route('admin.settings.promo_codes.index')
            ->with('success', 'Promo Code status updated successfully.');
    }
}
