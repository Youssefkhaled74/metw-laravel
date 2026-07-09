<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Enum\ShipmentRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\RejectionReason;
use App\Models\ShipmentRequest;
use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingRequestController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function incoming(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);
            $this->metwGoCourierService->assertApproved($representative);

            $availability = $this->metwGoCourierService->availabilityStatus($representative);

            if ($availability !== 'online') {
                return responseJson(false, 'الرجاء التفعيل على الإنترنت لعرض الطلبات.', [
                    'status' => 'offline',
                ], 403);
            }

            $limit = (int) $request->input('limit', 10);
            $page = (int) $request->input('page', 1);

            $requests = $this->metwGoCourierService->incomingShippingRequestsQuery($representative)
                ->paginate($limit, ['*'], 'page', $page);

            $data = collect($requests->items())
                ->map(fn ($req) => $this->metwGoCourierService->formatShippingRequest($req))
                ->values();

            return responseJson(true, '', [
                'data' => $data,
                'meta' => [
                    'current_page' => $requests->currentPage(),
                    'has_more' => $requests->hasMorePages(),
                    'total' => $requests->total(),
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');
            return responseJson(false, 'لا يمكن عرض الطلبات قبل الموافقة على الحساب.', [
                'status' => $status,
            ], 403);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'لم يتم العثور على حساب مندوب.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function show($requestId, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->metwGoCourierService->assertRepresentativeExists($user);

            $shipmentRequest = ShipmentRequest::with([
                'senderContact.primaryAddress.governorate',
                'senderContact.primaryAddress.city',
                'receiverContact.primaryAddress.governorate',
                'receiverContact.primaryAddress.city',
                'packages',
            ])->findOrFail($requestId);

            $details = $this->metwGoCourierService->formatShippingRequestDetails($shipmentRequest);

            return responseJson(true, '', $details, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'Shipping request not found.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function start($requestId, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);
            $this->metwGoCourierService->assertApproved($representative);

            $availability = $this->metwGoCourierService->availabilityStatus($representative);

            if ($availability !== 'online') {
                return responseJson(false, 'لا يمكن بدء الطلب وأنت غير متصل.', [
                    'status' => 'offline',
                ], 403);
            }

            $hasActive = $this->metwGoCourierService->activeShippingRequestQuery($representative)->exists();

            if ($hasActive) {
                return responseJson(false, 'You already have an active shipping request.', null, 409);
            }

            $shipmentRequest = ShipmentRequest::where('id', $requestId)
                ->whereNull('representative_id')
                ->where('status', ShipmentRequestStatus::SUBMITTED->value)
                ->firstOrFail();

            $shipmentRequest->update([
                'representative_id' => $representative->id,
                'status' => ShipmentRequestStatus::ASSIGNED,
                'accepted_at' => now(),
            ]);

            return responseJson(true, 'تم بدء طلب الشحن', [
                'active_shipping_request' => [
                    'id' => $shipmentRequest->id,
                    'request_number' => $shipmentRequest->request_number,
                    'status' => ShipmentRequestStatus::ASSIGNED->value,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'This shipping request has already been taken by another courier.', null, 409);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');
            return responseJson(false, 'لا يمكن بدء الطلب قبل الموافقة على الحساب.', [
                'status' => $status,
            ], 403);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function reject($requestId, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);
            $this->metwGoCourierService->assertApproved($representative);

            $validated = $request->validate([
                'reason_id' => ['required', 'integer', 'exists:rejection_reasons,id'],
                'custom_reason' => ['nullable', 'string', 'max:500'],
            ]);

            $reason = RejectionReason::findOrFail($validated['reason_id']);

            $shipmentRequest = ShipmentRequest::where('id', $requestId)
                ->whereNull('representative_id')
                ->where('status', ShipmentRequestStatus::SUBMITTED->value)
                ->firstOrFail();

            $shipmentRequest->update([
                'status' => ShipmentRequestStatus::REJECTED,
                'rejection_reason_id' => $validated['reason_id'],
                'rejection_note' => $validated['custom_reason'] ?? null,
                'rejected_at' => now(),
            ]);

            return responseJson(true, 'تم رفض طلب الشحن', [
                'request_id' => (int) $shipmentRequest->id,
                'status' => ShipmentRequestStatus::REJECTED->value,
                'reason' => $reason->reason_text,
                'custom_reason' => $validated['custom_reason'] ?? null,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (str_contains($e->getMessage(), 'RejectionReason')) {
                return responseJson(false, 'سبب الرفض غير موجود.', null, 404);
            }
            return responseJson(false, 'لم يتم العثور على الطلب أو تم أخذه بواسطة مندوب آخر.', null, 409);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');
            if ($status) {
                return responseJson(false, 'لا يمكن رفض الطلب قبل الموافقة على الحساب.', [
                    'status' => $status,
                ], 403);
            }
            throw $e;
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function active(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $activeRequest = $this->metwGoCourierService->activeShippingRequestQuery($representative)
                ->first();

            if (!$activeRequest) {
                return responseJson(true, '', ['active_shipping_request' => null], 200);
            }

            return responseJson(true, '', [
                'active_shipping_request' => $this->metwGoCourierService->formatShippingRequest($activeRequest),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'لم يتم العثور على حساب مندوب.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
