<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enum\RepresentativeStatus;
use App\Http\Controllers\Controller;
use App\Models\Representative;
use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminMetwGoController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function pendingCouriers(): JsonResponse
    {
        try {
            $representatives = Representative::query()
                ->withoutGlobalScopes()
                ->with(['user', 'workTypes.option', 'governorates', 'cities', 'vehicle.transportType', 'mediaFiles'])
                ->where('status', RepresentativeStatus::PENDING_APPROVAL->value)
                ->latest()
                ->get()
                ->map(fn (Representative $rep) => $this->formatPendingCourier($rep));

            return responseJson(true, '', [
                'couriers' => $representatives,
                'total' => $representatives->count(),
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function approve(Representative $representative): JsonResponse
    {
        try {
            if ($representative->status?->value !== RepresentativeStatus::PENDING_APPROVAL->value) {
                return responseJson(false, 'المندوب ليس في حالة انتظار موافقة.', null, 422);
            }

            $representative->update([
                'status' => RepresentativeStatus::ACTIVE,
                'reviewed_at' => now(),
                'approved_at' => now(),
                'suspended_at' => null,
                'is_active' => true,
            ]);

            $representative->load(['user', 'workTypes.option', 'governorates', 'cities', 'vehicle.transportType', 'mediaFiles']);

            return responseJson(true, 'تم اعتماد المندوب بنجاح', [
                'courier' => $this->formatPendingCourier($representative),
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function reject(Request $request, Representative $representative): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => ['required', 'string', 'max:1000'],
            ]);

            if ($representative->status?->value !== RepresentativeStatus::PENDING_APPROVAL->value) {
                return responseJson(false, 'المندوب ليس في حالة انتظار موافقة.', null, 422);
            }

            $representative->update([
                'status' => RepresentativeStatus::REJECTED,
                'reviewed_at' => now(),
                'approved_at' => null,
                'suspended_at' => null,
                'rejection_reason' => $validated['rejection_reason'],
                'is_active' => false,
            ]);

            return responseJson(true, 'تم رفض المندوب بنجاح', null, 200);
        } catch (ValidationException $e) {
            return responseJson(false, $e->getMessage(), $e->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    private function formatPendingCourier(Representative $representative): array
    {
        $fullName = trim(collect([
            $representative->first_name,
            $representative->father_name,
            $representative->last_name,
        ])->filter()->implode(' '));

        return [
            'id' => $representative->id,
            'account_number' => $representative->account_number,
            'name' => $fullName,
            'phone' => $representative->phone,
            'email' => $representative->user?->email,
            'status' => $representative->status?->value ?? $representative->status,
            'is_profile_complete' => (bool) $representative->is_profile_complete,
            'created_at' => $representative->created_at?->toIso8601String(),
        ];
    }
}
