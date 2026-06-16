<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\ContactAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactAdminController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.contact-admins.index')) {
            return view('dashboard.admin.no-permission');
        }

        $contacts = ContactAdmin::withoutGlobalScope('active')->latest()->paginate(15);

        return view('dashboard.admin.settings.contact_admins.index', compact('contacts'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.contact-admins.create')) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.contact_admins.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.contact-admins.store')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255|unique:contact_admins,name',
            'value'     => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        ContactAdmin::create($data);

        return redirect()
            ->route('admin.settings.contact-admins.index')
            ->with('success', 'Contact setting created successfully.');
    }

    public function edit(ContactAdmin $contactAdmin)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.contact-admins.edit')) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.contact_admins.edit', [
            'contact' => $contactAdmin,
        ]);
    }

    public function update(Request $request, ContactAdmin $contactAdmin)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.contact-admins.update')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255|unique:contact_admins,name,' . $contactAdmin->id,
            'value'     => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $contactAdmin->update($data);

        return redirect()
            ->route('admin.settings.contact-admins.index')
            ->with('success', 'Contact setting updated successfully.');
    }

    public function destroy(ContactAdmin $contactAdmin)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.contact-admins.destroy')) {
            return view('dashboard.admin.no-permission');
        }

        $contactAdmin->delete();

        return redirect()
            ->route('admin.settings.contact-admins.index')
            ->with('success', 'Contact setting deleted successfully.');
    }

    public function toggleStatus(ContactAdmin $contactAdmin)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.contact-admins.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }

        $contactAdmin->update([
            'is_active' => !$contactAdmin->is_active,
        ]);

        return redirect()
            ->route('admin.settings.contact-admins.index')
            ->with('success', 'Contact setting status updated successfully.');
    }
}



