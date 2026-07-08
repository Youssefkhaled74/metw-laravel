<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Http\Controllers\Controller;
use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $this->metwGoCourierService->assertApproved($representative);

            $courier = $this->metwGoCourierService->formatCourier($representative);

            $wallet = $user->wallet;
            $incomingOrders = $this->metwGoCourierService->incomingOrdersQuery($representative)
                ->limit(10)
                ->get()
                ->map(fn ($order) => $this->metwGoCourierService->formatOrderItem($order))
                ->values();

            $activeOrder = $this->metwGoCourierService->activeOrderQuery($representative)
                ->first();

            $unreadCount = $this->metwGoCourierService->unreadNotificationsCount($user);

            return responseJson(true, '', [
                'courier' => $courier,
                'stats' => [
                    'today_earnings' => (float) ($representative->metadata['today_earnings'] ?? 0),
                    'completed_orders_today' => (int) ($representative->metadata['completed_orders_today'] ?? 0),
                    'planned_orders_today' => (int) ($representative->metadata['planned_orders_today'] ?? 0),
                    'wallet_balance' => (float) ($wallet?->balance ?? 0),
                ],
                'notifications' => [
                    'unread_count' => $unreadCount,
                ],
                'incoming_orders' => $incomingOrders,
                'active_order' => $activeOrder ? $this->metwGoCourierService->formatOrderItem($activeOrder) : null,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');
            $messages = [
                'pending_approval' => 'حسابك قيد المراجعة.',
                'rejected' => 'تم رفض الحساب.',
                'suspended' => 'تم إيقاف الحساب.',
            ];
            return responseJson(false, $messages[$status] ?? 'الحساب غير مفعل.', [
                'status' => $status,
            ], 403);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'لم يتم العثور على حساب مندوب.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function setAvailability(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validate([
                'status' => ['required', 'in:online,offline'],
            ]);

            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            if ($validated['status'] === 'online') {
                $this->metwGoCourierService->assertApproved($representative);
            }

            $representative = $this->metwGoCourierService->setAvailability($representative, $validated['status']);

            return responseJson(true, 'تم تحديث حالة الاستقبال', [
                'availability_status' => $this->metwGoCourierService->availabilityStatus($representative),
                'online_started_at' => $this->metwGoCourierService->onlineStartedAt($representative),
                'online_duration_seconds' => $this->metwGoCourierService->onlineDurationSeconds($representative),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');
            return responseJson(false, 'لا يمكن تفعيل الحساب قبل الموافقة عليه.', [
                'status' => $status,
            ], 403);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'لم يتم العثور على حساب مندوب.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
