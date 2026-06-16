<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $wallet = Wallet::where('user_id', $user->id)
            ->with(['transactions' => function ($q) {
                $q->latest()->limit(10);
            }])->first();

        if (!$wallet) {
            return response()->json([
                'message' => 'Wallet not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'success',
            'data' => [
                'id' => $wallet->id,
                'balance' => $wallet->balance,
                'currency' => $wallet->currency,
                'transactions' => $wallet->transactions->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'amount' => $t->amount,
                        'type' => $t->type,
                        'description' => $t->description,
                        'reference_id' => $t->reference_id,
                        'created_at' => $t->created_at,
                    ];
                }),
            ]
        ]);
    }

    public function transactions(Request $request)
    {
        $user = Auth::user();

        $perPage = $request->input('per_page', 10);
        $transactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'message' => 'success',
            'data' => $transactions
        ]);
    }
}
