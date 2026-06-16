<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Vendor;

class ProfileController extends Controller
{
    public function index()
    {
        $vendor = auth('vendor')->user();
        return view('dashboard.vendor.profile.index', compact('vendor'));
    }

    public function update(Request $request)
    {
        $vendor = auth('vendor')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email,' . $vendor->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = uploadImage($request, 'logo', 'storage/vendors');
            $validated['logo'] = $path;
        }

        $vendor->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Profile updated successfully');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $vendor = auth('vendor')->user();

        if (!Hash::check($validated['current_password'], $vendor->password)) {
            return redirect()
                ->back()
                ->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        $vendor->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()
            ->back()
            ->with('success', 'Password changed successfully');
    }
}
