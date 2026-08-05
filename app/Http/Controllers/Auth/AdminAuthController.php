<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = trim((string) $request->input('username'));
        $password = (string) $request->input('password');
        $remember = $request->boolean('remember');

        $credentialSets = [
            ['username' => $identifier, 'password' => $password],
            ['email' => $identifier, 'password' => $password],
            ['phone' => $identifier, 'password' => $password],
        ];

        foreach ($credentialSets as $credentials) {
            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                $request->session()->regenerate();
                return $this->redirectToDashboard('admin');
            }
        }

        $admin = Admin::query()
            ->where('username', $identifier)
            ->orWhere('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if ($admin && ! $admin->is_active) {
            return back()
                ->withErrors(['username' => 'This admin account is inactive.'])
                ->withInput($request->except('password'));
        }

        return back()->withErrors(['username' => 'Invalid credentials'])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
