<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ShipmentCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Notifications\NewShipmentCompanyRegistered;

class ShipmentAuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $type = $request->segment(1);
        return view('auth.shipment.login' , compact('type'));
    }

    public function login(Request $request)
    {
        $type = $request->segment(1);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $ShipmentCompany = ShipmentCompany::where('email', $credentials['email'])->first();

        if (!$ShipmentCompany) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        if (!$ShipmentCompany->is_active) {
            return back()->withErrors(['email' => 'Your account is inactive'])->withInput();
        }

        if (Auth::guard('shipment')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('shipment.dashboard'));
        }

        if (Auth::guard($type)->attempt($credentials)) {
            $request->session()->regenerate();

            // Ensure we're properly authenticated
            if (Auth::guard($type)->check()) {
                // Clear any previous intended URLs to prevent redirect loops
                if ($request->session()->has('url.intended')) {
                    $request->session()->forget('url.intended');
                }

                // Direct redirect to dashboard
                return redirect()->route("$type.dashboard");
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function showRegisterForm(Request $request)
    {
        $type = $request->segment(1);
        return view('auth.shipment.register', compact('type'));
    }


    public function register(Request $request)
    {
        $type = $request->segment(1);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:shipment_companies,email',
            'password' => 'required|string|min:6|confirmed',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
        ]);

        $company = ShipmentCompany::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'phone'     => $data['phone'] ?? null,
            'address'   => $data['address'] ?? null,
            'is_active' => false,
        ]);

        // 🔔 Notify all admins (same like vendor)
        foreach (Admin::all() as $admin) {
            $admin->notify(new NewShipmentCompanyRegistered($company));
        }

        return redirect()
            ->route("$type.login")
            ->with(
                'success',
                'Your registration request has been submitted. Please wait until the admin approves your account before logging in.'
            );
    }


    public function logout(Request $request)
    {
        $type = $request->segment(1);

        Auth::guard($type)->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("$type.login");
    }
}
