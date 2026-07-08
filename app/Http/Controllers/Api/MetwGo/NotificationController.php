<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Http\Controllers\Controller;
use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function count(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $count = $this->metwGoCourierService->unreadNotificationsCount($user);

            return responseJson(true, '', [
                'unread_count' => $count,
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
