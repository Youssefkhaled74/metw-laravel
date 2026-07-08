<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MetwGo\MetwGoWithdrawalRequest;
use App\Models\WalletWithdrawal;
use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function summary(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $wallet = $user->wallet;

            return responseJson(true, '', [
                'balance' => (float) ($wallet?->balance ?? 0),
                'currency' => $wallet?->currency ?? 'EGP',
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function withdrawals(MetwGoWithdrawalRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();

            $wallet = $user->wallet;

            if (!$wallet || (float) $wallet->balance < (float) $validated['amount']) {
                return responseJson(false, 'رصيد المحفظة غير كافٍ.', null, 422);
            }

            $withdrawal = WalletWithdrawal::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'account_reference' => $validated['account_reference'],
                'status' => 'pending',
            ]);

            return responseJson(true, 'تم إرسال طلب سحب الرصيد', [
                'withdrawal_id' => $withdrawal->id,
                'status' => 'pending',
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
