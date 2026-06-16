<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Vendor;
use App\Notifications\NewVendorRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class VendorAuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $type = $request->segment(1);
        return view('auth.vendor.login' , compact('type'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $vendor = Vendor::where('email', $request->email)->first();
        if (!$vendor) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        if (!$vendor->is_active) {
            return back()->withErrors(['email' => 'Your account is inactive'])->withInput();
        }

        $credentials = $request->only('email', 'password');
        if (Auth::guard('vendor')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return $this->redirectToDashboard('vendor');
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('vendor.login');
    }

    public function showRegistrationForm()
    {
        return view('auth.vendor.register');
    }

    public function register(Request $request)
    {
        $type = $request->segment(1);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:vendors',
            'phone' => 'required|string|max:20|unique:vendors',
            'country_code' => 'required|string|max:10',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($request->hasFile('logo')) {
            $imagePath = uploadImage($request, 'logo', 'storage/vendors');
            $data['logo'] = $imagePath;
        }

        $vendor = Vendor::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'country_code' => $data['country_code'],
            'password' => Hash::make($data['password']),
            'address' => $data['address'],
            'logo' => $data['logo'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'is_active' => false,
        ]);

        // 🔔 Notify all admins
        foreach (Admin::all() as $admin) {
            $admin->notify(new NewVendorRegistered($vendor));
        }

        // Auth::guard($type)->login($vendor);

        // $request->session()->regenerate();

        return redirect()->route("$type.login")
            ->with('success', 'Your registration request has been submitted. Please wait until the admin approves your account before logging in.');
    }
}
