<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorBranch;
use App\Models\State;
use App\Models\City;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,active,inactive'],
            'sort_by' => ['nullable', 'in:id,name'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $branchesQuery = VendorBranch::where('vendor_id', auth('vendor')->id())
            ->with(['state', 'city', 'zone']);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $branchesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('street_main', 'like', "%{$search}%")
                    ->orWhere('street_sub', 'like', "%{$search}%")
                    ->orWhere('building', 'like', "%{$search}%")
                    ->orWhere('id', is_numeric($search) ? (int) $search : 0)
                    ->orWhereHas('state', function ($stateQuery) use ($search) {
                        $stateQuery->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    })
                    ->orWhereHas('city', function ($cityQuery) use ($search) {
                        $cityQuery->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $branchesQuery->where('status', $validated['status'] === 'active');
        }

        $branches = $branchesQuery
            ->orderBy($sortBy, $sortDir)
            ->paginate(10)
            ->appends(request()->query());

        return view('dashboard.vendor.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $states = State::active()->get();

        return view('dashboard.vendor.branches.create', compact('states'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'zone_id' => 'required|exists:zones,id',
            'street_main' => 'required|string|max:255',
            'street_sub' => 'nullable|string|max:255',
            'building' => 'required|string|max:50',
            'building_name' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['vendor_id'] = auth('vendor')->id();
        $data['status'] = $request->has('status') ? 1 : 0;

        VendorBranch::create($data);

        return redirect()->route('vendor.branches')
            ->with('success', __('vendor-dashboard.branch_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(VendorBranch $branch)
    {
        // Check ownership
        if ($branch->vendor_id !== auth('vendor')->id()) {
            abort(403);
        }

        return view('dashboard.vendor.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VendorBranch $branch)
    {
        // Check ownership
        if ($branch->vendor_id !== auth('vendor')->id()) {
            abort(403);
        }

        $states = State::active()->get();
        $cities = City::where('state_id', $branch->state_id)->active()->get();
        $zones = Zone::where('city_id', $branch->city_id)->active()->get();

        return view('dashboard.vendor.branches.edit', compact('branch', 'states', 'cities', 'zones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VendorBranch $branch)
    {
        // Check ownership
        if ($branch->vendor_id !== auth('vendor')->id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'zone_id' => 'required|exists:zones,id',
            'street_main' => 'required|string|max:255',
            'street_sub' => 'nullable|string|max:255',
            'building' => 'required|string|max:50',
            'building_name' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['status'] = $request->has('status') ? 1 : 0;

        $branch->update($data);

        return redirect()->route('vendor.branches')
            ->with('success', __('vendor-dashboard.branch_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VendorBranch $branch)
    {
        // Check ownership
        if ($branch->vendor_id !== auth('vendor')->id()) {
            abort(403);
        }

        $branch->delete();

        return redirect()->route('vendor.branches')
            ->with('success', __('vendor-dashboard.branch_deleted_successfully'));
    }

    /**
     * API: Get cities by state
     */
    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)
            ->active()
            ->get(['id', 'name_en', 'name_ar']);

        return response()->json($cities);
    }

    /**
     * API: Get zones by city
     */
    public function getZones($city_id)
    {
        $zones = Zone::where('city_id', $city_id)
            ->active()
            ->get(['id', 'name_en', 'name_ar']);

        return response()->json($zones);
    }
}
