<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Governorate;
use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function warehouses(): JsonResponse
    {
        try {
            $warehouses = $this->metwGoCourierService->warehouseList();

            return responseJson(true, '', $warehouses, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function transportTypes(): JsonResponse
    {
        try {
            $types = $this->metwGoCourierService->transportTypeList();

            return responseJson(true, '', $types, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function governorates(): JsonResponse
    {
        try {
            $governorates = Governorate::query()
                ->orderBy('name_ar')
                ->get()
                ->map(fn ($gov) => [
                    'id' => $gov->id,
                    'name' => $gov->name_ar,
                ])
                ->values();

            return responseJson(true, '', $governorates, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function cities(Request $request): JsonResponse
    {
        try {
            $governorateIds = $request->input('governorate_ids', []);

            if (empty($governorateIds)) {
                return responseJson(false, 'يجب تحديد المحافظات.', null, 422);
            }

            $cities = City::query()
                ->whereIn('governorate_id', $governorateIds)
                ->orderBy('name_ar')
                ->get()
                ->map(fn ($city) => [
                    'id' => $city->id,
                    'governorate_id' => $city->governorate_id,
                    'name' => $city->name_ar,
                ])
                ->values();

            return responseJson(true, '', $cities, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
