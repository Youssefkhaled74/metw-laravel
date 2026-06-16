<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        try {
            if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.users')) {
                return view('dashboard.admin.no-permission');
            }

            $validated = request()->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'sort_by' => ['nullable', 'in:user_number,username,created_at'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDir = $validated['sort_dir'] ?? 'desc';

            $usersQuery = User::query()
                ->with(['wallet'])
                ->withCount(['orders', 'ecommerceOrders']);

            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $usersQuery->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $users = $usersQuery
                ->orderBy($sortBy, $sortDir)
                ->paginate(20)
                ->appends(request()->query());

            return view('dashboard.admin.users', compact('users', 'sortBy', 'sortDir'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    public function show($id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.users.show')) {
            return view('dashboard.admin.no-permission');
        }
        $user = User::with(['addresses', 'orders.shipmentCompany', 'ecommerceOrders'])
            ->withCount(['orders', 'ecommerceOrders'])
            ->findOrFail($id);

        return view('dashboard.admin.user-details', compact('user'));
    }

    public function updateWalletBalance(Request $request, $id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.users')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'operation' => ['required', 'in:add,subtract'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);

        $user = User::with('wallet')->findOrFail($id);

        DB::transaction(function () use ($user, $data) {
            /** @var Wallet $wallet */
            $wallet = $user->wallet ?: $user->wallet()->create([
                'balance' => 0,
                'currency' => 'EGP',
                'is_active' => true,
            ]);

            $current = (float) $wallet->balance;
            $amount = (float) $data['amount'];

            $new = $data['operation'] === 'add'
                ? $current + $amount
                : $current - $amount;

            $wallet->update([
                'balance' => $new,
                'is_active' => true,
            ]);

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'reference_id' => Str::random(12),
                'amount' => $amount,
                'description' => 'Wallet balance ' . ($data['operation'] === 'add' ? 'increased' : 'decreased') . ' by admin',
                'type' => $data['operation'] === 'add' ? 'increase' : 'decrease',
            ]);
        });

        return back()->with('success', __('admin-dashboard.wallet_balance_updated'));
    }
}
