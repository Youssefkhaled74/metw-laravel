<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $type = $request->segment(1); // 'admin' or 'vendor' or 'employee'
        return view('auth.login', compact('type'));
    }

    public function login(Request $request)
    {
        $type = $request->segment(1);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Detect login guard
        $guards = match ($type) {
            'admin' => ['admin', 'employee'],
            'vendor' => ['vendor'],
            default => [$type],
        };

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->attempt($credentials)) {
                $user = Auth::guard($guard)->user();

                // Check permission if guard is employee
                if ($guard === 'employee' && !$user->can('admin.login')) {
                    Auth::guard($guard)->logout(); // log out immediately
                    return back()->withErrors([
                        'email' => 'You do not have permission to login as admin.'
                    ])->withInput($request->except('password'));
                }

                $request->session()->regenerate();

                $routeName = $guard === 'vendor' ? 'vendor.dashboard' : 'admin.dashboard';
                return redirect()->route($routeName);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }



    public function logout(Request $request)
    {
        $type = $request->segment(1); // 'admin', 'employee', or 'vendor'
        $guardUser = Auth::guard($type)->user();

        // Only check permission if a user is logged in and guard is employee
        if ($type === 'employee' && $guardUser && !$guardUser->can('admin.logout')) {
            return back()->withErrors([
                'error' => 'You do not have permission to logout.'
            ]);
        }

        // Proceed only if a user is logged in
        if ($guardUser) {
            Auth::guard($type)->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("$type.login");
    }


}
